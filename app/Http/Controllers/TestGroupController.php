<?php

namespace App\Http\Controllers;

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

            $query = TestGroup::with('category')
                ->select('test_groups.*');

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

                ->addColumn('category_name', function ($group) {
                    return $group->category?->name ?? '-';
                })

                ->addColumn('status_badge', function ($group) {
                    return view('pages.test_groups.partials.status', compact('group'))->render();
                })

                ->addColumn('action', function ($group) {
                    return view('pages.test_groups.partials.actions', compact('group'))->render();
                })

                ->rawColumns(['status_badge', 'action'])
                ->make(true);
        }

        return view('pages.test_groups.index');
    }

    public function create()
    {
        $categories = TestCategory::orderBy('name')->get();
        return view('pages.test_groups.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => ['nullable', 'exists:test_categories,id'],
            'name' => ['required', 'string', 'max:150', 'unique:test_groups,name'],
            'price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        TestGroup::create($data);

        return redirect()->route('test-groups.index')
            ->with('success', 'Test group created successfully.');
    }

    public function edit(TestGroup $testGroup)
    {
        $categories = TestCategory::orderBy('name')->get();
        return view('pages.test_groups.edit', compact('testGroup', 'categories'));
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
        ]);

        $testGroup->update($data);

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