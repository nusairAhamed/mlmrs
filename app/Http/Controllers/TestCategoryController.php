<?php

namespace App\Http\Controllers;

use App\Models\TestCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class TestCategoryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $categories = TestCategory::query()->select('test_categories.*');

            // Filter by name
            if ($request->filled('name')) {
                $categories->where('name', 'like', '%' . $request->name . '%');
            }

            return DataTables::of($categories)
                ->addColumn('action', function ($category) {
                    return view('pages.test_categories.partials.actions', compact('category'))->render();
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('pages.test_categories.index');
    }

    public function create()
    {
        return view('pages.test_categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:100','unique:test_categories,name'],
          
        ]);

       

        TestCategory::create($data);

        return redirect()->route('test-categories.index')
            ->with('success', 'Test category created successfully.');
    }

    public function edit(TestCategory $test_category)
    {
        return view('pages.test_categories.edit', ['category' => $test_category]);
    }

    public function update(Request $request, TestCategory $test_category)
    {
        $data = $request->validate([
            'name' => ['required','string','max:100', Rule::unique('test_categories','name')->ignore($test_category->id)],
      
        ]);

        
        $test_category->update($data);

        return redirect()->route('test-categories.index')
            ->with('success', 'Test category updated successfully.');
    }

    public function destroy(TestCategory $test_category)
    {
        $test_category->delete();

        return redirect()->route('test-categories.index')
            ->with('success', 'Test category deleted successfully.');
    }
}