@php
    $labOrder = $labOrder ?? null;
    $isEdit = !is_null($labOrder);

    // ✅ passed from edit.blade.php
    $allowPanelEdit = $allowPanelEdit ?? false;

    // ✅ panels are locked on edit unless explicitly allowed
    $panelsLocked = $isEdit && !$allowPanelEdit;

    $selectedGroupIds = $selectedGroupIds ?? [];
    $selected = old('group_ids', $selectedGroupIds);

    $groups = $groups ?? collect();
    $patients = $patients ?? collect();
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    {{-- Patient --}}
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Patient</label>

        <select name="patient_id"
                class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
            <option value="">-- Select Patient --</option>
            @foreach($patients as $p)
                <option value="{{ $p->id }}"
                    @selected(old('patient_id', $labOrder->patient_id ?? '') == $p->id)>
                    {{ $p->patient_code ?? '' }}{{ isset($p->patient_code) ? ' — ' : '' }}{{ $p->full_name ?? ($p->name ?? '-') }}
                </option>
            @endforeach
        </select>

        @error('patient_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Notes --}}
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>

        <textarea name="notes" rows="3"
                  class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">{{ old('notes', $labOrder->notes ?? '') }}</textarea>

        @error('notes')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Panels --}}
    <div class="md:col-span-2">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2 mb-2">
            <label class="block text-sm font-medium text-gray-700">Select Test Panels</label>

            <div class="flex items-center gap-4">
                <span id="selected-count" class="text-xs font-medium text-gray-600">
                    Selected: {{ is_array($selected) ? count($selected) : 0 }}
                </span>
                <span id="total-amount" class="text-xs font-semibold text-gray-800">
                    Total: Rs 0.00
                </span>
            </div>
        </div>

        <p class="text-xs text-gray-500 mb-3">
            If selected panels contain overlapping tests, duplicates will be performed only once (first selected panel wins).
        </p>

        <input type="text" id="panel-search" placeholder="Search panels..."
               class="w-full mb-3 rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
               @disabled($panelsLocked)>

        <div class="flex flex-wrap items-center gap-2 mb-3">
            <button type="button" id="select-all-visible"
                    class="rounded-lg px-3 py-1.5 text-xs font-semibold ring-1 ring-gray-300 hover:bg-gray-50"
                    @disabled($panelsLocked)>
                Select all (visible)
            </button>

            <button type="button" id="clear-all"
                    class="rounded-lg px-3 py-1.5 text-xs font-semibold text-red-700 ring-1 ring-red-200 hover:bg-red-50"
                    @disabled($panelsLocked)>
                Clear all
            </button>

            @if($panelsLocked)
                <span class="text-xs text-gray-500">(Panels locked)</span>
            @elseif($isEdit && $allowPanelEdit)
                <span class="text-xs text-gray-500">(Panels editable — pending)</span>
            @endif
        </div>

        <div id="panels-grid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">
            @foreach($groups as $g)
                @php
                    $checked = in_array($g->id, $selected ?? []);
                    $price = (float)($g->price ?? 0);

                    $max = 5;
                    $testNames = ($g->tests ?? collect())->pluck('name');
                    $shown = $testNames->take($max);
                    $more = max(0, $testNames->count() - $max);
                @endphp

                <label class="panel-card group block rounded-2xl border bg-white p-4 shadow-sm transition
                              {{ $panelsLocked ? 'opacity-60 cursor-not-allowed' : 'cursor-pointer' }}
                              {{ $checked ? 'border-indigo-300 ring-2 ring-indigo-500/20' : 'border-gray-200 hover:border-gray-300 hover:ring-2 hover:ring-indigo-500/10' }}"
                       data-name="{{ strtolower($g->name) }}">

                    <div class="flex items-start gap-3">
                        <input type="checkbox"
                               class="panel-checkbox mt-1 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                               name="group_ids[]"
                               value="{{ $g->id }}"
                               data-price="{{ $price }}"
                               @checked($checked)
                               @disabled($panelsLocked)>

                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <div class="text-sm font-semibold text-gray-900 truncate">
                                        {{ $g->name }}
                                    </div>
                                </div>

                                <div class="shrink-0 text-right">
                                    <div class="text-sm font-bold text-gray-900">
                                        Rs {{ number_format($price, 2) }}
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3 flex flex-wrap items-center gap-2">
                                @if(isset($g->tests_count))
                                    <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-700 ring-1 ring-gray-200">
                                        {{ $g->tests_count }} tests
                                    </span>
                                @endif

                                <span class="inline-flex items-center rounded-full bg-green-50 px-2.5 py-1 text-xs font-medium text-green-700 ring-1 ring-green-200">
                                    Active
                                </span>
                            </div>

                            {{-- Test list --}}
                            <div class="mt-3 max-h-24 overflow-hidden">
                                <div class="text-xs font-medium text-gray-700 mb-1">Includes:</div>

                                <div class="flex flex-wrap gap-2">
                                    @forelse($shown as $t)
                                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-1 text-xs text-gray-700 ring-1 ring-gray-200">
                                            {{ $t }}
                                        </span>
                                    @empty
                                        <span class="text-xs text-gray-500">No tests linked</span>
                                    @endforelse

                                    @if($more > 0)
                                        <span class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-medium text-indigo-700 ring-1 ring-indigo-200">
                                            + {{ $more }} more
                                        </span>
                                    @endif
                                </div>
                            </div>

                        </div>
                    </div>
                </label>
            @endforeach
        </div>

        @error('group_ids')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
        @error('group_ids.*')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>

{{-- ✅ JS should run when panels are editable: create OR edit-allowed --}}
@if(!$panelsLocked)
<script>
(function () {
    const search = document.getElementById('panel-search');
    const countEl = document.getElementById('selected-count');
    const totalEl = document.getElementById('total-amount');
    const selectAllBtn = document.getElementById('select-all-visible');
    const clearAllBtn = document.getElementById('clear-all');

    const cards = () => Array.from(document.querySelectorAll('.panel-card'));
    const checkboxes = () => Array.from(document.querySelectorAll('.panel-checkbox'));

    function updateCardStyles() {
        cards().forEach(card => {
            const cb = card.querySelector('.panel-checkbox');
            if (!cb) return;

            card.classList.remove('border-indigo-300','ring-2','ring-indigo-500/20');
            card.classList.remove('border-gray-200','hover:border-gray-300','hover:ring-2','hover:ring-indigo-500/10');

            if (cb.checked) {
                card.classList.add('border-indigo-300','ring-2','ring-indigo-500/20');
            } else {
                card.classList.add('border-gray-200','hover:border-gray-300','hover:ring-2','hover:ring-indigo-500/10');
            }
        });
    }

    function updateStats() {
        const selected = checkboxes().filter(cb => cb.checked);
        countEl.textContent = `Selected: ${selected.length}`;

        const total = selected.reduce((sum, cb) => {
            const price = parseFloat(cb.dataset.price || '0');
            return sum + (isNaN(price) ? 0 : price);
        }, 0);

        totalEl.textContent = `Total: Rs ${total.toFixed(2)}`;
        updateCardStyles();
    }

    function applySearch() {
        const q = (search?.value || '').toLowerCase().trim();
        cards().forEach(card => {
            const name = (card.dataset.name || '').toLowerCase();
            card.style.display = name.includes(q) ? '' : 'none';
        });
    }

    document.addEventListener('change', (e) => {
        if (e.target && e.target.classList && e.target.classList.contains('panel-checkbox')) {
            updateStats();
        }
    });

    search?.addEventListener('input', applySearch);

    selectAllBtn?.addEventListener('click', () => {
        cards().forEach(card => {
            if (card.style.display === 'none') return;
            const cb = card.querySelector('.panel-checkbox');
            if (cb) cb.checked = true;
        });
        updateStats();
    });

    clearAllBtn?.addEventListener('click', () => {
        checkboxes().forEach(cb => cb.checked = false);
        updateStats();
    });

    applySearch();
    updateStats();
})();
</script>
@endif