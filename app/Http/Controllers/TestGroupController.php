<?php

namespace App\Http\Controllers;

use App\Models\Test;
use App\Models\TestCategory;
use App\Models\TestGroup;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class TestGroupController extends Controller
{
    public function index(Request $request)
{
    if ($request->ajax()) {

        $query = TestGroup::with(['category', 'tests:id,name'])
        ->withCount('tests')
        ->select('test_groups.*');

        // Filters
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return DataTables::of($query)
            ->addColumn('category_name', fn ($group) => $group->category?->name ?? '-')
            ->addColumn('tests_info', function ($group) {
                $count = (int) ($group->tests->count() ?? 0);
                $testNames = $group->tests?->pluck('name')->join(', ') ?? '';

                return '<span class="inline-flex items-center rounded-full bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-700 ring-1 ring-indigo-200 cursor-pointer"
                            title="'.e($testNames).'">
                            '.$count.'
                        </span>';
            })
            ->addColumn('status_badge', fn ($group) => view('pages.test_groups.partials.status', compact('group'))->render())
            ->addColumn('action', fn ($group) => view('pages.test_groups.partials.actions', compact('group'))->render())
            ->rawColumns(['tests_info', 'status_badge', 'action'])
            ->make(true);
    }

    return view('pages.test_groups.index');
}

    public function create()
    {
        $categories = TestCategory::orderBy('name')->get();
        $tests = Test::where('status', 'active')->orderBy('name')->get(); // ✅ added

        return view('pages.test_groups.create', compact('categories', 'tests'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => ['nullable', 'exists:test_categories,id'],
            'name' => ['required', 'string', 'max:150', 'unique:test_groups,name'],
            'price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(['active', 'inactive'])],

            // ✅ added for pivot
            'test_ids' => ['nullable', 'array'],
            'test_ids.*' => ['integer', 'exists:tests,id'],
        ]);

        $testIds = $data['test_ids'] ?? [];
        unset($data['test_ids']);

        $group = TestGroup::create($data);
        $group->tests()->sync($testIds); // ✅ save pivot

        return redirect()->route('test-groups.index')
            ->with('success', 'Test group created successfully.');
    }

    public function edit(TestGroup $testGroup)
    {
        $testGroup->load('tests:id'); // ✅ added
        $categories = TestCategory::orderBy('name')->get();
        $tests = Test::where('status', 'active')->orderBy('name')->get(); // ✅ added

        return view('pages.test_groups.edit', compact('testGroup', 'categories', 'tests'));
    }

    public function update(Request $request, TestGroup $testGroup)
    {
        $data = $request->validate([
            'category_id' => ['nullable', 'exists:test_categories,id'],
            'name' => [
                'required', 'string', 'max:150',
                Rule::unique('test_groups', 'name')->ignore($testGroup->id),
            ],
            'price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(['active', 'inactive'])],

            // ✅ added for pivot
            'test_ids' => ['nullable', 'array'],
            'test_ids.*' => ['integer', 'exists:tests,id'],
        ]);

        $testIds = $data['test_ids'] ?? [];
        unset($data['test_ids']);

        $testGroup->update($data);
        $testGroup->tests()->sync($testIds); // ✅ update pivot

        return redirect()->route('test-groups.index')
            ->with('success', 'Test group updated successfully.');
    }

    public function destroy(TestGroup $testGroup)
    {
        $testGroup->delete();

        return redirect()->route('test-groups.index')
            ->with('success', 'Test group deleted successfully.');
    }
}