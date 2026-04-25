<div class="bg-white rounded-lg shadow-sm ring-1 ring-gray-200 p-6 mb-6">
    <form id="patients-filter-form" class="grid grid-cols-1 md:grid-cols-4 gap-4">

        {{-- Patient Code --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Patient Code
            </label>
            <input type="text" name="patient_code"
                   class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm
                          focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
        </div>

        {{-- Name --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Full Name
            </label>
            <input type="text" name="full_name"
                   class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm
                          focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
        </div>

        {{-- Phone --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Phone
            </label>
            <input type="text" name="phone"
                   class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm
                          focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
        </div>

        {{-- Gender --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Gender
            </label>
            <select name="gender"
                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm
                           focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
                <option value="">All</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="other">Other</option>
            </select>
        </div>

        <div class="md:col-span-4 flex items-center gap-2 pt-2">
            <button type="submit"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                </svg>
                Apply
            </button>
            <button type="button" id="reset-patients-filter"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-white px-4 py-2 text-sm font-medium text-gray-700 ring-1 ring-gray-200 hover:bg-gray-50 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/>
                </svg>
                Reset
            </button>
        </div>
    </form>
</div>