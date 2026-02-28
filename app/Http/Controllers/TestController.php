<?php

namespace App\Http\Controllers;

use App\Models\Test;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\Facades\DataTables;

class TestController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('draw')) {

            $query = Test::query()->select('tests.*');

            // Filters
            if ($request->filled('name')) {
                $query->where('name', 'like', '%' . $request->name . '%');
            }
            if ($request->filled('data_type')) {
                $query->where('data_type', $request->data_type);
            }
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            return DataTables::of($query)
                ->editColumn('data_type', fn ($t) => ucfirst($t->data_type))
                ->editColumn('unit', fn ($t) => $t->unit ?: '-')
                ->addColumn('status_badge', fn ($test) => view('pages.tests.partials.status', compact('test'))->render())
                ->addColumn('action', fn ($test) => view('pages.tests.partials.actions', compact('test'))->render())
                ->rawColumns(['status_badge', 'action'])
                ->make(true);
        }

        return view('pages.tests.index');
    }

    public function create()
    {
        return view('pages.tests.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateTest($request);

        DB::transaction(function () use ($data) {
            $test = Test::create($data['test']);

            if ($test->data_type === 'numeric') {
                $test->ranges()->createMany($data['ranges']);
            }
        });

        return redirect()->route('tests.index')->with('success', 'Test created successfully.');
    }

    public function edit(Test $test)
    {
        $test->load('ranges');
        return view('pages.tests.edit', compact('test'));
    }

    public function update(Request $request, Test $test)
    {
        $data = $this->validateTest($request, $test->id);

        DB::transaction(function () use ($data, $test) {
            $test->update($data['test']);

            // Replace ranges fully (simple + safe for capstone)
            $test->ranges()->delete();

            if ($test->data_type === 'numeric') {
                $test->ranges()->createMany($data['ranges']);
            }
        });

        return redirect()->route('tests.index')->with('success', 'Test updated successfully.');
    }

    public function destroy(Test $test)
    {
        DB::transaction(function () use ($test) {
            $test->ranges()->delete();
            $test->delete();
        });

        return redirect()->route('tests.index')->with('success', 'Test deleted successfully.');
    }

    private function validateTest(Request $request, ?int $ignoreId = null): array
    {
        $test = $request->validate([
            'name' => [
                'required', 'string', 'max:150',
                Rule::unique('tests', 'name')->ignore($ignoreId),
            ],
            'unit' => ['nullable', 'string', 'max:50'],
            'data_type' => ['required', Rule::in(['numeric', 'text'])],
            'sort_order' => ['required', 'integer', 'min:0'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $ranges = [];

        if ($test['data_type'] === 'numeric') {

            $request->validate([
                'ranges' => ['required', 'array', 'min:1'],
                'ranges.*.gender' => ['required', Rule::in(['any', 'male', 'female'])],
                'ranges.*.age_min' => ['nullable', 'integer', 'min:0'],
                'ranges.*.age_max' => ['nullable', 'integer', 'min:0'],
                'ranges.*.ref_min' => ['nullable', 'numeric'],
                'ranges.*.ref_max' => ['nullable', 'numeric'],
            ]);

            $ranges = collect($request->input('ranges', []))
                ->map(function ($r) {
                    $r['gender'] = $r['gender'] ?? 'any';

                    $r['age_min'] = ($r['age_min'] === '' || $r['age_min'] === null) ? null : (int) $r['age_min'];
                    $r['age_max'] = ($r['age_max'] === '' || $r['age_max'] === null) ? null : (int) $r['age_max'];

                    $r['ref_min'] = ($r['ref_min'] === '' || $r['ref_min'] === null) ? null : (float) $r['ref_min'];
                    $r['ref_max'] = ($r['ref_max'] === '' || $r['ref_max'] === null) ? null : (float) $r['ref_max'];

                    return $r;
                })
                // Drop completely empty rows (all 4 empty)
                ->filter(function ($r) {
                    return !(
                        $r['age_min'] === null &&
                        $r['age_max'] === null &&
                        $r['ref_min'] === null &&
                        $r['ref_max'] === null
                    );
                })
                ->values()
                ->all();

            if (count($ranges) === 0) {
                throw ValidationException::withMessages([
                    'ranges' => ['At least one reference range is required for numeric tests.'],
                ]);
            }

            // ✅ ADD: strict range validations (min<=max, duplicates, overlaps)
            $this->validateReferenceRanges($ranges);
        }

        return [
            'test' => $test,
            'ranges' => $ranges,
        ];
    }

    /**
     * Validates:
     * 1) age_min <= age_max
     * 2) ref_min <= ref_max
     * 3) duplicates (same gender + same age range)
     * 4) overlaps per gender (and 'any' conflicts with male/female)
     */
    private function validateReferenceRanges(array $ranges): void
    {
        $messages = [];

        // Helper: normalized interval for overlap checks
        $normAge = function ($row) {
            $min = ($row['age_min'] === null) ? 0 : (int) $row['age_min'];
            $max = ($row['age_max'] === null) ? PHP_INT_MAX : (int) $row['age_max'];
            return [$min, $max];
        };

        $overlaps = function ($aMin, $aMax, $bMin, $bMax) {
            // inclusive overlap: [aMin,aMax] intersects [bMin,bMax]
            return max($aMin, $bMin) <= min($aMax, $bMax);
        };

        // 1) min<=max checks + require at least one ref bound
        foreach ($ranges as $i => $r) {

            if ($r['age_min'] !== null && $r['age_max'] !== null && $r['age_min'] > $r['age_max']) {
                $messages["ranges.$i.age_max"][] = 'Age Max must be greater than or equal to Age Min.';
            }

            if ($r['ref_min'] !== null && $r['ref_max'] !== null && $r['ref_min'] > $r['ref_max']) {
                $messages["ranges.$i.ref_max"][] = 'Ref Max must be greater than or equal to Ref Min.';
            }

            // If user adds an age row but no reference values, it’s meaningless
            if ($r['ref_min'] === null && $r['ref_max'] === null) {
                $messages["ranges.$i.ref_min"][] = 'Provide at least Ref Min or Ref Max.';
                $messages["ranges.$i.ref_max"][] = 'Provide at least Ref Min or Ref Max.';
            }
        }

        // Stop early if basic errors exist
        if (!empty($messages)) {
            throw ValidationException::withMessages($messages);
        }

        // 2) duplicate checks (same gender + same age interval)
        $seen = [];
        foreach ($ranges as $i => $r) {
            [$aMin, $aMax] = $normAge($r);

            $key = implode('|', [
                $r['gender'],
                $aMin,
                $aMax,
                // including ref bounds makes duplicate stricter; remove these two if you only care about age duplicates
                ($r['ref_min'] === null ? 'null' : $r['ref_min']),
                ($r['ref_max'] === null ? 'null' : $r['ref_max']),
            ]);

            if (isset($seen[$key])) {
                $messages["ranges.$i.gender"][] = 'Duplicate reference range row detected.';
            } else {
                $seen[$key] = $i;
            }
        }

        // 3) overlap checks per gender (and 'any' conflicts with male/female)
        // We check overlaps inside:
        // - male: compare male vs male AND male vs any
        // - female: compare female vs female AND female vs any
        // - any: compare any vs any (already covered, but keep clean)
        $bucket = [
            'male' => [],
            'female' => [],
            'any' => [],
        ];

        foreach ($ranges as $i => $r) {
            $bucket[$r['gender']][] = ['i' => $i, 'row' => $r];
        }

        $checkPairList = function (array $listA, array $listB) use ($normAge, $overlaps, &$messages) {
            foreach ($listA as $x => $a) {
                [$aMin, $aMax] = $normAge($a['row']);

                foreach ($listB as $y => $b) {
                    // avoid double-check when same list
                    if ($listA === $listB && $y <= $x) continue;

                    [$bMin, $bMax] = $normAge($b['row']);

                    if ($overlaps($aMin, $aMax, $bMin, $bMax)) {
                        $messages["ranges.{$a['i']}.age_min"][] = 'Age range overlaps with another rule for this gender.';
                        $messages["ranges.{$b['i']}.age_min"][] = 'Age range overlaps with another rule for this gender.';
                    }
                }
            }
        };

        // overlaps within same gender
        $checkPairList($bucket['male'], $bucket['male']);
        $checkPairList($bucket['female'], $bucket['female']);
        $checkPairList($bucket['any'], $bucket['any']);

        // overlaps with "any" (any conflicts with male & female)
        $checkPairList($bucket['male'], $bucket['any']);
        $checkPairList($bucket['female'], $bucket['any']);

        if (!empty($messages)) {
            throw ValidationException::withMessages($messages);
        }
    }
}