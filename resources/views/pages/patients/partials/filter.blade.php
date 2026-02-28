<div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 p-6 mb-6">
    <form id="patients-filter-form" class="grid grid-cols-1 md:grid-cols-4 gap-4">

        {{-- Patient Code --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Patient Code
            </label>
            <input type="text" name="patient_code"
                   class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm
                          focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
        </div>

        {{-- Name --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Full Name
            </label>
            <input type="text" name="full_name"
                   class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm
                          focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
        </div>

        {{-- Phone --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Phone
            </label>
            <input type="text" name="phone"
                   class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm
                          focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
        </div>

        {{-- Gender --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Gender
            </label>
            <select name="gender"
                    class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm
                           focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
                <option value="">All</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="other">Other</option>
            </select>
        </div>

        <div class="md:col-span-4 flex items-center gap-2 pt-2">
            <button type="submit"
                    class="px-4 py-2 bg-black text-white rounded-lg">
                Apply Filters
            </button>

            <button type="button" id="reset-patients-filter"
                    class="px-4 py-2 bg-gray-100 text-gray-800 rounded-lg hover:bg-gray-200">
                Reset
            </button>
        </div>
    </form>
</div>