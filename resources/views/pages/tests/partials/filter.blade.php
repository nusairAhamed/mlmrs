<div class="bg-white rounded-lg shadow-sm ring-1 ring-gray-200 p-6 mb-6">
    <form id="tests-filter-form" class="grid grid-cols-1 md:grid-cols-4 gap-4">

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
            <input type="text" name="name"
                   class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
            <select name="data_type"
                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
                <option value="">All</option>
                <option value="numeric">Numeric</option>
                <option value="text">Text</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select name="status"
                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
                <option value="">All</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>

        <div class="flex items-end gap-2">
            <button type="submit"
                    class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition">
                Apply
            </button>

            <button type="button" id="reset-tests-filter"
                    class="rounded-lg bg-white px-4 py-2 text-sm font-medium text-gray-700 ring-1 ring-gray-200 hover:bg-gray-50 transition">
                Reset
            </button>
        </div>

    </form>
</div>