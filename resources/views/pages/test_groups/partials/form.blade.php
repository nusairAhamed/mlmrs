@php
    $selectedTests = old(
        'test_ids',
        isset($testGroup) ? $testGroup->tests->pluck('id')->toArray() : []
    );
@endphp


<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    {{-- Name --}}
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Group Name
        </label>
        <input type="text"
               name="name"
               value="{{ old('name', $testGroup->name ?? '') }}"
               class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">

        @error('name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Category --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Category
        </label>
        <select name="category_id"
                class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
            <option value="">-- None --</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}"
                    @selected(old('category_id', $testGroup->category_id ?? '') == $category->id)>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>

        @error('category_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Price --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Price
        </label>
        <input type="number"
               name="price"
               step="0.01"
               min="0"
               value="{{ old('price', $testGroup->price ?? 0) }}"
               class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">

        @error('price')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Status --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Status
        </label>
        <select name="status"
                class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
            <option value="active"
                @selected(old('status', $testGroup->status ?? 'active') === 'active')>
                Active
            </option>
            <option value="inactive"
                @selected(old('status', $testGroup->status ?? 'active') === 'inactive')>
                Inactive
            </option>
        </select>

        @error('status')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Tests Multi Select --}}
    <div class="md:col-span-2">
    <div class="flex items-center justify-between mb-2">
        <label class="block text-sm font-medium text-gray-700">
            Tests in this Group
        </label>

        <span id="selected-count"
              class="text-xs font-medium text-gray-600">
            Selected: {{ count($selectedTests) }}
        </span>
    </div>

    <input type="text"
           id="test-search"
           placeholder="Search tests..."
           class="w-full mb-3 rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">

    <div class="flex items-center gap-2 mb-3">
        <button type="button" id="select-all"
                class="rounded-lg px-3 py-1.5 text-xs font-semibold ring-1 ring-gray-300 hover:bg-gray-50">
            Select all (visible)
        </button>

        <button type="button" id="clear-all"
                class="rounded-lg px-3 py-1.5 text-xs font-semibold text-red-700 ring-1 ring-red-200 hover:bg-red-50">
            Clear all
        </button>
    </div>

    <div id="tests-box"
         class="max-h-72 overflow-y-auto rounded-xl border border-gray-200 bg-gray-50 p-3 space-y-2">
        @foreach($tests as $test)
            <label class="flex items-center gap-2 rounded-lg px-2 py-1 hover:bg-white test-item">
                <input type="checkbox"
                       class="test-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                       name="test_ids[]"
                       value="{{ $test->id }}"
                       @checked(in_array($test->id, $selectedTests))>

                <span class="text-sm text-gray-800">
                    {{ $test->name }}
                    @if($test->unit)
                        <span class="text-gray-500">({{ $test->unit }})</span>
                    @endif
                </span>
            </label>
        @endforeach
    </div>

    @error('test_ids')
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

</div>


<script>
(function () {
    const search = document.getElementById('test-search');
    const countEl = document.getElementById('selected-count');
    const checkboxes = Array.from(document.querySelectorAll('.test-checkbox'));
    const items = Array.from(document.querySelectorAll('.test-item'));
    const selectAllBtn = document.getElementById('select-all');
    const clearAllBtn = document.getElementById('clear-all');

    function updateCount() {
        const selected = checkboxes.filter(cb => cb.checked).length;
        countEl.textContent = `Selected: ${selected}`;
    }

    function applyFilter() {
        const q = (search.value || '').toLowerCase().trim();
        items.forEach(item => {
            item.style.display = item.innerText.toLowerCase().includes(q) ? '' : 'none';
        });
    }

    // live search
    search?.addEventListener('input', applyFilter);

    // update count on change
    checkboxes.forEach(cb => cb.addEventListener('change', updateCount));

    // select all visible
    selectAllBtn?.addEventListener('click', () => {
        items.forEach((item, idx) => {
            if (item.style.display === 'none') return;
            checkboxes[idx].checked = true;
        });
        updateCount();
    });

    // clear all
    clearAllBtn?.addEventListener('click', () => {
        checkboxes.forEach(cb => cb.checked = false);
        updateCount();
    });

    // initial
    applyFilter();
    updateCount();
})();
</script>