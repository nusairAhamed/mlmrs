<div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 p-6 mb-6">
    <form id="filter-form" class="grid grid-cols-1 md:grid-cols-4 gap-4">

        {{-- Name --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Name
            </label>
            <input type="text" name="name"
                class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm
                        focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
        </div>

        {{-- Email --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Email
            </label>
            <input type="text" name="email"
                class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm
                        focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
        </div>

        {{-- Role --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Role
            </label>
            <select name="role"
                    class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm
                        focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
                <option value="">All</option>
                <option value="admin">Admin</option>
                <option value="receptionist">Receptionist</option>
                <option value="lab">Lab</option>
            </select>
        </div>

        {{-- Buttons --}}
        <div class="flex items-end gap-2">
            <button type="submit"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-xl text-sm">
                Apply
            </button>

            <button type="button" id="reset-filter"
                    class="px-4 py-2 bg-gray-200 rounded-xl text-sm">
                Reset
            </button>
        </div>

    </form>
</div>