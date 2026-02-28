<div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 p-6 mb-6">
    <form id="test-groups-filter-form" class="grid grid-cols-1 md:grid-cols-4 gap-4">

        {{-- Name --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Name
            </label>
            <input type="text" name="name"
                   class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm
                          focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
        </div>

        {{-- Category --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Category
            </label>
            <select name="category_id"
                    class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm
                           focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
                <option value="">All</option>
                @foreach(\App\Models\TestCategory::orderBy('name')->get() as $c)
                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Status --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Status
            </label>
            <select name="status"
                    class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm
                           focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
                <option value="">All</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>

        {{-- Buttons --}}
        <div class="flex items-end gap-2">
            <button type="submit"
                    class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                Apply
            </button>

            <button type="button" id="reset-test-groups-filter"
                    class="rounded-xl px-4 py-2 text-sm font-semibold ring-1 ring-gray-300 hover:bg-gray-50">
                Reset
            </button>
        </div>

    </form>
</div>