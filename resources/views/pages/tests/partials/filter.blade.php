<div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 p-6 mb-6">
    <form id="tests-filter-form" class="grid grid-cols-1 md:grid-cols-4 gap-4">

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
            <input type="text" name="name"
                   class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
            <select name="data_type"
                    class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm">
                <option value="">All</option>
                <option value="numeric">Numeric</option>
                <option value="text">Text</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select name="status"
                    class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm">
                <option value="">All</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>

        <div class="flex items-end gap-2">
            <button type="submit"
                    class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white">
                Apply
            </button>

            <button type="button" id="reset-tests-filter"
                    class="rounded-xl px-4 py-2 text-sm font-semibold ring-1 ring-gray-300">
                Reset
            </button>
        </div>

    </form>
</div>