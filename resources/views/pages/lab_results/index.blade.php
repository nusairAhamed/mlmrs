<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800">
                Enter Results — {{ $labOrder->order_number }}
            </h2>

            <div class="flex items-center gap-2">
                <a href="{{ route('lab-orders.show', $labOrder) }}"
                   class="rounded-xl bg-white px-4 py-2 text-sm font-semibold text-gray-700 ring-1 ring-gray-200 hover:bg-gray-50">
                    Back to Order
                </a>
            </div>
        </div>
    </x-slot>

    @php
        $totalTests = $labOrder->tests->count();
        $verifiedCount = $labOrder->tests->where('status', 'verified')->count();
        $abnormalCount = $labOrder->tests->where('is_abnormal', true)->count();

        function badgeClasses($status) {
            return match($status) {
                'verified' => 'bg-green-50 text-green-700 ring-green-200',
                'entered' => 'bg-yellow-50 text-yellow-700 ring-yellow-200',
                default => 'bg-gray-50 text-gray-700 ring-gray-200',
            };
        }

        function formatRefRange($min, $max) {
            if (is_null($min) && is_null($max)) {
                return '-';
            }

            $format = function ($value) {
                if (is_null($value)) {
                    return '-';
                }

                return rtrim(rtrim(number_format((float) $value, 4, '.', ''), '0'), '.');
            };

            return $format($min) . ' – ' . $format($max);
        }

        function abnormalLabel($test) {
            if (!$test->is_abnormal) {
                return ['label' => 'Normal', 'class' => 'bg-green-50 text-green-700 ring-green-200'];
            }

            if (
                ($test->test?->data_type ?? 'text') === 'numeric' &&
                !is_null($test->result_value) &&
                is_numeric($test->result_value)
            ) {
                $value = (float) $test->result_value;

                if (!is_null($test->ref_min) && $value < (float) $test->ref_min) {
                    return ['label' => 'Low', 'class' => 'bg-blue-50 text-blue-700 ring-blue-200'];
                }

                if (!is_null($test->ref_max) && $value > (float) $test->ref_max) {
                    return ['label' => 'High', 'class' => 'bg-red-50 text-red-700 ring-red-200'];
                }
            }

            return ['label' => 'Abnormal', 'class' => 'bg-red-50 text-red-700 ring-red-200'];
        }
    @endphp

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 rounded-xl bg-green-50 p-4 text-green-700 ring-1 ring-green-200">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 rounded-xl bg-red-50 p-4 text-red-700 ring-1 ring-red-200">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 rounded-xl bg-red-50 p-4 text-red-700 ring-1 ring-red-200">
                    {{ $errors->first() }}
                </div>
            @endif

            {{-- Summary --}}
            <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <div class="text-xs text-gray-500">Patient</div>
                        <div class="text-sm font-semibold text-gray-900">
                            {{ $labOrder->patient?->patient_code ?? '' }}{{ $labOrder->patient?->patient_code ? ' — ' : '' }}
                            {{ $labOrder->patient?->full_name ?? '-' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs text-gray-500">Order Status</div>
                        <div class="mt-1">
                            @include('pages.lab_orders.partials.status', ['order' => $labOrder])
                        </div>
                    </div>

                    <div>
                        <div class="text-xs text-gray-500">Verified Progress</div>
                        <div class="text-sm font-semibold text-gray-900">
                            {{ $verifiedCount }}/{{ $totalTests }} Verified
                        </div>
                        <div class="mt-2 h-2 w-full overflow-hidden rounded-full bg-gray-100">
                            <div class="h-2 rounded-full bg-indigo-600"
                                 style="width: {{ $totalTests > 0 ? ($verifiedCount / $totalTests) * 100 : 0 }}%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="text-xs text-gray-500">Abnormal Results</div>
                        <div class="text-sm font-semibold {{ $abnormalCount > 0 ? 'text-red-600' : 'text-gray-900' }}">
                            {{ $abnormalCount }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Save all form --}}
            <form method="POST" action="{{ route('lab-results.bulk-update', $labOrder) }}">
                @csrf
                @method('PATCH')

                @foreach($labOrder->groups as $group)
                    @php
                        $groupAbnormalCount = $group->tests->where('is_abnormal', true)->count();
                    @endphp

                    <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 p-6 mb-6">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900">
                                    {{ $group->testGroup?->name ?? 'Panel' }}
                                </h3>

                                @if($groupAbnormalCount > 0)
                                    <div class="mt-2 inline-flex rounded-full bg-red-50 px-3 py-1 text-xs font-medium text-red-700 ring-1 ring-red-200">
                                        {{ $groupAbnormalCount }} abnormal result{{ $groupAbnormalCount > 1 ? 's' : '' }} in this panel
                                    </div>
                                @endif
                            </div>

                            <div class="text-xs text-gray-500">
                                Tests: {{ $group->tests->count() }}
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="text-left text-gray-600">
                                    <tr>
                                        <th class="py-3 pr-4">Test</th>
                                        <th class="py-3 pr-4">Unit</th>
                                        <th class="py-3 pr-4">Ref</th>
                                        <th class="py-3 pr-4">Result</th>
                                        <th class="py-3 pr-4">Status</th>
                                        <th class="py-3 pr-4">Abnormal</th>
                                        <th class="py-3 pr-4 text-center">Verify</th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y divide-gray-100">
                                    @forelse($group->tests as $t)
                                        @php
                                            $isApproved = $labOrder->status === 'approved';
                                            $isVerified = $t->status === 'verified';
                                            $dataType = $t->test?->data_type ?? 'text';
                                            $abnormal = abnormalLabel($t);
                                            $canVerify = !$isApproved && !$isVerified && !is_null($t->result_value) && $t->result_value !== '';
                                        @endphp

                                        <tr class="{{ $t->is_abnormal ? 'bg-red-50/40' : '' }}">
                                            <td class="py-3 pr-4">
                                                <div class="font-medium text-gray-900">{{ $t->test_name }}</div>
                                            </td>

                                            <td class="py-3 pr-4 text-gray-700">
                                                {{ $t->unit ?? '-' }}
                                            </td>

                                            <td class="py-3 pr-4 text-gray-700 whitespace-nowrap">
                                                {{ formatRefRange($t->ref_min, $t->ref_max) }}
                                            </td>

                                            <td class="py-3 pr-4">
                                                @if($dataType === 'numeric')
                                                    <input type="number"
                                                           step="any"
                                                           name="results[{{ $t->id }}]"
                                                           value="{{ old('results.' . $t->id, $t->result_value) }}"
                                                           class="w-36 rounded-lg border {{ $t->is_abnormal ? 'border-red-300 bg-red-50' : 'border-gray-200' }} px-3 py-1.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 disabled:bg-gray-100 disabled:text-gray-500"
                                                           placeholder="Enter result"
                                                           @disabled($isApproved || $isVerified)>
                                                @else
                                                    <input type="text"
                                                           name="results[{{ $t->id }}]"
                                                           value="{{ old('results.' . $t->id, $t->result_value) }}"
                                                           class="w-40 rounded-lg border {{ $t->is_abnormal ? 'border-red-300 bg-red-50' : 'border-gray-200' }} px-3 py-1.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 disabled:bg-gray-100 disabled:text-gray-500"
                                                           placeholder="Enter result"
                                                           @disabled($isApproved || $isVerified)>
                                                @endif

                                                @if($isVerified)
                                                    <div class="mt-1 text-xs text-gray-500">
                                                        Locked after verification
                                                    </div>
                                                @endif
                                            </td>

                                            <td class="py-3 pr-4">
                                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium ring-1 {{ badgeClasses($t->status) }}">
                                                    {{ ucfirst($t->status) }}
                                                </span>
                                            </td>

                                            <td class="py-3 pr-4">
                                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium ring-1 {{ $abnormal['class'] }}">
                                                    {{ $abnormal['label'] }}
                                                </span>
                                            </td>

                                            <td class="py-3 pr-4 text-center">
                                                @if($isVerified)
                                                    <input type="checkbox"
                                                           checked
                                                           disabled
                                                           class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                                @else
                                                    <input type="checkbox"
                                                           form="verify-selected-form"
                                                           name="verify_ids[]"
                                                           value="{{ $t->id }}"
                                                           class="verify-checkbox rounded border-gray-300 text-green-600 focus:ring-green-500"
                                                           @disabled(!$canVerify)>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="py-3 text-gray-500">No tests in this panel.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach

                {{-- Bottom action bar --}}
                <div class="sticky bottom-4 z-10 mt-6">
                    <div class="flex flex-col gap-3 rounded-2xl bg-white/95 p-4 shadow-lg ring-1 ring-gray-200 backdrop-blur sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-3">
                            <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                                <input type="checkbox"
                                       id="select-all-eligible"
                                       class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                <span>Select all eligible</span>
                            </label>

                            <span class="text-xs text-gray-500">
                                Verified rows stay locked. Only rows with results can be selected.
                            </span>
                        </div>

                        <div class="flex items-center justify-end gap-2">
                            <button type="submit"
                                    class="rounded-xl bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-black disabled:pointer-events-none disabled:opacity-50"
                                    @disabled($labOrder->status === 'approved')>
                                Save All Results
                            </button>

                            <button type="submit"
                                    form="verify-selected-form"
                                    class="rounded-xl px-4 py-2 text-sm font-semibold text-green-700 ring-1 ring-green-200 hover:bg-green-50 disabled:pointer-events-none disabled:opacity-50"
                                    @disabled($labOrder->status === 'approved')>
                                Verify Selected
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            {{-- Verify selected form --}}
            <form id="verify-selected-form" method="POST" action="{{ route('lab-results.bulk-verify', $labOrder) }}">
                @csrf
                @method('PATCH')
            </form>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selectAll = document.getElementById('select-all-eligible');
            const checkboxes = Array.from(document.querySelectorAll('.verify-checkbox:not(:disabled)'));

            if (!selectAll || checkboxes.length === 0) {
                return;
            }

            selectAll.addEventListener('change', function () {
                checkboxes.forEach((checkbox) => {
                    checkbox.checked = selectAll.checked;
                });
            });

            checkboxes.forEach((checkbox) => {
                checkbox.addEventListener('change', function () {
                    const allChecked = checkboxes.length > 0 && checkboxes.every(cb => cb.checked);
                    selectAll.checked = allChecked;
                });
            });
        });
    </script>
</x-app-layout>