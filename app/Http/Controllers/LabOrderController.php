<?php

namespace App\Http\Controllers;

use App\Models\LabOrder;
use App\Models\LabOrderGroup;
use App\Models\LabOrderTest;
use App\Models\Patient;
use App\Models\TestGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class LabOrderController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = LabOrder::query()
                ->with(['patient'])
                ->select('lab_orders.*');

            if ($request->filled('order_number')) {
                $query->where('order_number', 'like', '%' . $request->order_number . '%');
            }

            if ($request->filled('patient_id')) {
                $query->where('patient_id', $request->patient_id);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            return DataTables::of($query)
                ->addColumn('patient_name', fn ($o) => $o->patient?->full_name ?? $o->patient?->name ?? '-')
                ->addColumn('status_badge', fn ($o) => view('pages.lab_orders.partials.status', ['order' => $o])->render())
                ->addColumn('action', fn ($o) => view('pages.lab_orders.partials.actions', ['order' => $o])->render())
                ->rawColumns(['status_badge', 'action'])
                ->make(true);
        }

        $patients = Patient::orderBy('full_name')->get(['id', 'full_name', 'patient_code']);

        return view('pages.lab_orders.index', compact('patients'));
    }

    public function create()
    {
        $patients = Patient::orderBy('full_name')->get(['id', 'full_name', 'patient_code']);

        $groups = TestGroup::withCount('tests')
            ->with(['tests:id,name']) // many-to-many ok
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'price', 'status']);

        return view('pages.lab_orders.create', compact('patients', 'groups'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'group_ids' => ['required', 'array', 'min:1'],
            'group_ids.*' => ['integer', 'exists:test_groups,id'],
        ]);

        return DB::transaction(function () use ($data) {

            // ensure unique order number
            do {
                $orderNumber = 'LO-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
            } while (LabOrder::where('order_number', $orderNumber)->exists());

            $order = LabOrder::create([
                'patient_id' => $data['patient_id'],
                'created_by' => auth()->id(),
                'order_number' => $orderNumber,
                'status' => 'pending',
                'total_amount' => 0,
                'notes' => $data['notes'] ?? null,
            ]);

            // load selected groups + tests
            $groups = TestGroup::with(['tests:id,name,unit'])
                ->whereIn('id', $data['group_ids'])
                ->orderBy('name')
                ->get(['id', 'name', 'price']);

            $total = 0;

            // overlap handling: skip duplicate tests across groups (first group wins)
            $insertedTestIds = [];

            foreach ($groups as $group) {
                $total += (float)($group->price ?? 0);

                $orderGroup = LabOrderGroup::create([
                    'lab_order_id' => $order->id,
                    'test_group_id' => $group->id,
                    'group_price_snapshot' => $group->price ?? 0,
                ]);

                foreach ($group->tests as $test) {
                    if (isset($insertedTestIds[$test->id])) {
                        continue;
                    }

                    LabOrderTest::create([
                        'lab_order_id' => $order->id,
                        'lab_order_group_id' => $orderGroup->id,
                        'test_id' => $test->id,

                        // snapshots
                        'test_name' => $test->name,
                        'unit' => $test->unit,

                        // range snapshot (fill later)
                        'test_reference_range_id' => null,
                        'ref_min' => null,
                        'ref_max' => null,

                        // workflow per test
                        'status' => 'pending',
                        'is_abnormal' => false,

                        // results empty until entry/verification
                        'result_value' => null,
                        'entered_by' => null,
                        'entered_at' => null,
                        'verified_by' => null,
                        'verified_at' => null,
                    ]);

                    $insertedTestIds[$test->id] = true;
                }
            }

            $order->update(['total_amount' => $total]);

            // ✅ IMPORTANT: NO lab_samples creation here.
            // Lab technician will generate samples later.

            return redirect()
                ->route('lab-orders.index')
                ->with('success', 'Lab Order created successfully.');
        });
    }

    public function show(LabOrder $labOrder)
    {
        $labOrder->load([
            'patient',
            'groups.testGroup',
            'tests',
            'samples', // safe: just display if generated
        ]);

        return view('pages.lab_orders.show', compact('labOrder'));
    }

    public function edit(LabOrder $labOrder)
    {
        if ($labOrder->status !== 'pending') {
            return redirect()
                ->route('lab-orders.index')
                ->with('error', 'Only pending orders can be edited.');
        }

        // block panel edits if any result already entered/verified
        $hasAnyProgress = $labOrder->tests()
            ->whereNotNull('result_value')
            ->orWhereNotNull('entered_at')
            ->orWhereNotNull('verified_at')
            ->exists();

        $patients = Patient::orderBy('full_name')->get(['id', 'full_name', 'patient_code']);

        $groups = TestGroup::withCount('tests')
            ->with(['tests:id,name'])
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'price', 'status']);

        $selectedGroupIds = $labOrder->groups()->pluck('test_group_id')->toArray();

        return view('pages.lab_orders.edit', compact(
            'labOrder',
            'patients',
            'groups',
            'selectedGroupIds',
            'hasAnyProgress'
        ));
    }

    public function update(Request $request, LabOrder $labOrder)
    {
        if ($labOrder->status !== 'pending') {
            return redirect()
                ->route('lab-orders.index')
                ->with('error', 'Only pending orders can be updated.');
        }

        $hasAnyProgress = $labOrder->tests()
            ->whereNotNull('result_value')
            ->orWhereNotNull('entered_at')
            ->orWhereNotNull('verified_at')
            ->exists();

        $rules = [
            'patient_id' => ['required', 'exists:patients,id'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', Rule::in(['pending', 'in_progress', 'completed', 'approved'])],
        ];

        // allow panel changes only if no results were entered
        if (!$hasAnyProgress) {
            $rules['group_ids'] = ['required', 'array', 'min:1'];
            $rules['group_ids.*'] = ['integer', 'exists:test_groups,id'];
        }

        $data = $request->validate($rules);

        return DB::transaction(function () use ($labOrder, $data, $hasAnyProgress) {

            if (!$hasAnyProgress && isset($data['group_ids'])) {

                // delete existing panels/tests
                $labOrder->tests()->delete();
                $labOrder->groups()->delete();

                // 🔒 DO NOT delete samples automatically.
                // If samples already exist, keep them (technician owns them).
                // If you want to block panel changes once samples exist, add a guard in edit/update.

                $groups = TestGroup::with(['tests:id,name,unit'])
                    ->whereIn('id', $data['group_ids'])
                    ->orderBy('name')
                    ->get(['id', 'name', 'price']);

                $total = 0;
                $insertedTestIds = [];

                foreach ($groups as $group) {
                    $total += (float)($group->price ?? 0);

                    $orderGroup = LabOrderGroup::create([
                        'lab_order_id' => $labOrder->id,
                        'test_group_id' => $group->id,
                        'group_price_snapshot' => $group->price ?? 0,
                    ]);

                    foreach ($group->tests as $test) {
                        if (isset($insertedTestIds[$test->id])) continue;

                        LabOrderTest::create([
                            'lab_order_id' => $labOrder->id,
                            'lab_order_group_id' => $orderGroup->id,
                            'test_id' => $test->id,

                            'test_name' => $test->name,
                            'unit' => $test->unit,

                            'test_reference_range_id' => null,
                            'ref_min' => null,
                            'ref_max' => null,

                            'status' => 'pending',
                            'is_abnormal' => false,

                            'result_value' => null,
                            'entered_by' => null,
                            'entered_at' => null,
                            'verified_by' => null,
                            'verified_at' => null,
                        ]);

                        $insertedTestIds[$test->id] = true;
                    }
                }

                $labOrder->update(['total_amount' => $total]);
            }

            // always update patient/notes/status
            $labOrder->update([
                'patient_id' => $data['patient_id'],
                'notes' => $data['notes'] ?? null,
                'status' => $data['status'],
            ]);

            return redirect()
                ->route('lab-orders.index')
                ->with('success', 'Lab Order updated successfully.');
        });
    }

    public function destroy(LabOrder $labOrder)
    {
        if ($labOrder->status !== 'pending') {
            return redirect()
                ->route('lab-orders.index')
                ->with('error', 'Only pending orders can be deleted.');
        }

        // optional: also delete child rows if FK cascade not set
        // $labOrder->tests()->delete();
        // $labOrder->groups()->delete();
        // $labOrder->samples()->delete();

        $labOrder->delete();

        return redirect()
            ->route('lab-orders.index')
            ->with('success', 'Lab Order deleted successfully.');
    }
}