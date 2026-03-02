<div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Order #</label>
        <input id="filter_order_number" type="text"
               class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
               placeholder="LO-20260301-XXXXX">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
        <select id="filter_status"
                class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
            <option value="">All</option>
            <option value="pending">Pending</option>
            <option value="in_progress">In Progress</option>
            <option value="completed">Completed</option>
            <option value="approved">Approved</option>
        </select>
    </div>

    <div class="flex gap-2 md:col-span-2">
        <button type="button" id="btn_apply_filters"
                class="rounded-xl bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-black">
            Apply
        </button>
        <button type="button" id="btn_clear_filters"
                class="rounded-xl bg-white px-4 py-2 text-sm font-semibold text-gray-700 ring-1 ring-gray-200 hover:bg-gray-50">
            Clear
        </button>
    </div>
</div>