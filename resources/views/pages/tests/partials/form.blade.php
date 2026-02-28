@php
    $isEdit = isset($test);

    // Ranges source priority:
    // 1) old('ranges') if validation failed
    // 2) $test->ranges (edit)
    // 3) default one blank row (create)
    $rangesSource = old('ranges');

    if ($rangesSource === null) {
        if ($isEdit) {
            $rangesSource = $test->ranges->map(function($r) {
                return [
                    'gender' => $r->gender,
                    'age_min' => $r->age_min,
                    'age_max' => $r->age_max,
                    'ref_min' => $r->ref_min,
                    'ref_max' => $r->ref_max,
                ];
            })->toArray();
        } else {
            $rangesSource = [['gender' => 'any', 'age_min' => null, 'age_max' => null, 'ref_min' => null, 'ref_max' => null]];
        }
    }

    // Ensure at least one row exists for UI
    if (!is_array($rangesSource) || count($rangesSource) === 0) {
        $rangesSource = [['gender' => 'any', 'age_min' => null, 'age_max' => null, 'ref_min' => null, 'ref_max' => null]];
    }
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-5">

    {{-- Name --}}
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
        <input name="name"
               value="{{ old('name', $test->name ?? '') }}"
               class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm
                      focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Unit --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Unit</label>
        <input name="unit"
               value="{{ old('unit', $test->unit ?? '') }}"
               class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm
                      focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
        @error('unit') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Data Type --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Data Type</label>
        <select name="data_type" id="data_type"
                class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm
                       focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
            <option value="numeric" @selected(old('data_type', $test->data_type ?? 'numeric') === 'numeric')>Numeric</option>
            <option value="text" @selected(old('data_type', $test->data_type ?? '') === 'text')>Text</option>
        </select>
        @error('data_type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Sort Order --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
        <input name="sort_order" type="number" min="0"
               value="{{ old('sort_order', $test->sort_order ?? 0) }}"
               class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm
                      focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
        @error('sort_order') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Status --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
        <select name="status"
                class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm
                       focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
            <option value="active" @selected(old('status', $test->status ?? 'active') === 'active')>Active</option>
            <option value="inactive" @selected(old('status', $test->status ?? '') === 'inactive')>Inactive</option>
        </select>
        @error('status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

</div>

{{-- Reference Ranges --}}
<div id="ranges-section" class="pt-2">
    <div class="flex items-center justify-between mb-3">
        <h3 class="text-sm font-semibold text-gray-800">Reference Ranges</h3>

        <button type="button" id="add-range"
                class="rounded-xl bg-indigo-600 px-3 py-2 text-xs font-semibold text-white hover:bg-indigo-700">
            + Add Range
        </button>
    </div>

    @error('ranges') <p class="mb-2 text-sm text-red-600">{{ $message }}</p> @enderror

    <div class="overflow-auto ring-1 ring-gray-200 rounded-2xl">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="text-left px-4 py-3">Gender</th>
                    <th class="text-left px-4 py-3">Age Min</th>
                    <th class="text-left px-4 py-3">Age Max</th>
                    <th class="text-left px-4 py-3">Ref Min</th>
                    <th class="text-left px-4 py-3">Ref Max</th>
                    <th class="text-right px-4 py-3">Remove</th>
                </tr>
            </thead>

            <tbody id="ranges-body" class="divide-y divide-gray-100">
                @foreach($rangesSource as $i => $r)
                    <tr class="range-row">
                        <td class="px-4 py-2">
                            <select name="ranges[{{ $i }}][gender]"
                                    class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm">
                                <option value="any" @selected(($r['gender'] ?? 'any') === 'any')>Any</option>
                                <option value="male" @selected(($r['gender'] ?? '') === 'male')>Male</option>
                                <option value="female" @selected(($r['gender'] ?? '') === 'female')>Female</option>
                            </select>
                        </td>

                        <td class="px-4 py-2">
                            <input type="number" min="0"
                                   name="ranges[{{ $i }}][age_min]"
                                   value="{{ $r['age_min'] ?? '' }}"
                                   class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm">
                        </td>

                        <td class="px-4 py-2">
                            <input type="number" min="0"
                                   name="ranges[{{ $i }}][age_max]"
                                   value="{{ $r['age_max'] ?? '' }}"
                                   class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm">
                        </td>

                        <td class="px-4 py-2">
                            <input type="number" step="0.01"
                                   name="ranges[{{ $i }}][ref_min]"
                                   value="{{ $r['ref_min'] ?? '' }}"
                                   class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm">
                        </td>

                        <td class="px-4 py-2">
                            <input type="number" step="0.01"
                                   name="ranges[{{ $i }}][ref_max]"
                                   value="{{ $r['ref_max'] ?? '' }}"
                                   class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm">
                        </td>

                        <td class="px-4 py-2 text-right">
                            <button type="button"
                                    class="remove-range p-2 rounded-lg bg-red-600 text-white hover:bg-red-700"
                                    title="Remove">
                                ✕
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <p class="mt-2 text-xs text-gray-500">
        Tip: Add multiple rules by Gender and Age (e.g., Male/Female or age-based ranges).
    </p>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dataTypeEl = document.getElementById('data_type');
        const rangesSection = document.getElementById('ranges-section');
        const rangesBody = document.getElementById('ranges-body');
        const addBtn = document.getElementById('add-range');

        function toggleRanges() {
            rangesSection.classList.toggle('hidden', dataTypeEl.value !== 'numeric');
        }

        function reindexRows() {
            const rows = rangesBody.querySelectorAll('tr.range-row');
            rows.forEach((row, i) => {
                row.querySelectorAll('select, input').forEach((el) => {
                    el.name = el.name.replace(/ranges\[\d+]/, `ranges[${i}]`);
                });
            });
        }

        function makeRow(index) {
            const tr = document.createElement('tr');
            tr.className = 'range-row';

            tr.innerHTML = `
                <td class="px-4 py-2">
                    <select name="ranges[${index}][gender]" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm">
                        <option value="any">Any</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </td>
                <td class="px-4 py-2">
                    <input type="number" min="0" name="ranges[${index}][age_min]"
                           class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm">
                </td>
                <td class="px-4 py-2">
                    <input type="number" min="0" name="ranges[${index}][age_max]"
                           class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm">
                </td>
                <td class="px-4 py-2">
                    <input type="number" step="0.01" name="ranges[${index}][ref_min]"
                           class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm">
                </td>
                <td class="px-4 py-2">
                    <input type="number" step="0.01" name="ranges[${index}][ref_max]"
                           class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm">
                </td>
                <td class="px-4 py-2 text-right">
                    <button type="button" class="remove-range p-2 rounded-lg bg-red-600 text-white hover:bg-red-700" title="Remove">✕</button>
                </td>
            `;

            return tr;
        }

        addBtn.addEventListener('click', function () {
            const index = rangesBody.querySelectorAll('tr.range-row').length;
            rangesBody.appendChild(makeRow(index));
        });

        rangesBody.addEventListener('click', function (e) {
            if (e.target.closest('.remove-range')) {
                const rows = rangesBody.querySelectorAll('tr.range-row');
                if (rows.length <= 1) {
                    alert('At least one range row is required for numeric tests.');
                    return;
                }
                e.target.closest('tr').remove();
                reindexRows();
            }
        });

        dataTypeEl.addEventListener('change', toggleRanges);
        toggleRanges();
    });
</script>
@endpush