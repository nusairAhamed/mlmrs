<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800">Lab Order: {{ $labOrder->order_number }}</h2>

            <div class="flex items-center gap-2">
                <a href="{{ route('lab-orders.edit', $labOrder) }}"
                   class="rounded-xl bg-white px-4 py-2 text-sm font-semibold text-gray-700 ring-1 ring-gray-200 hover:bg-gray-50">
                    Edit
                </a>

                <a href="{{ route('lab-orders.index') }}"
                   class="rounded-xl bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-black">
                    Back
                </a>
            </div>
        </div>
    </x-slot>

    @php
        $totalTests = $labOrder->tests->count();
        $enteredCount = $labOrder->tests->where('status', 'entered')->count();
        $verifiedCount = $labOrder->tests->where('status', 'verified')->count();
        $abnormalCount = $labOrder->tests->where('is_abnormal', true)->count();

        $allVerified = $totalTests > 0 && $verifiedCount === $totalTests;
        $canApprove = $labOrder->status === 'completed' && $allVerified && is_null($labOrder->approved_at);

        function testStatusBadge($status) {
            return match($status) {
                'verified' => 'bg-green-50 text-green-700 ring-green-200',
                'entered' => 'bg-yellow-50 text-yellow-700 ring-yellow-200',
                default => 'bg-gray-50 text-gray-700 ring-gray-200',
            };
        }

        function abnormalBadge($test) {
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

            {{-- Order Summary --}}
            <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <div class="text-xs text-gray-500">Patient</div>
                        <div class="text-sm font-semibold text-gray-900">
                            {{ $labOrder->patient?->full_name ?? '-' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs text-gray-500">Status</div>
                        <div class="mt-1">
                            @include('pages.lab_orders.partials.status', ['order' => $labOrder])
                        </div>
                    </div>

                    <div>
                        <div class="text-xs text-gray-500">Total Amount</div>
                        <div class="text-sm font-semibold text-gray-900">
                            Rs {{ number_format((float) $labOrder->total_amount, 2) }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs text-gray-500">Order Number</div>
                        <div class="text-sm font-semibold text-gray-900">
                            {{ $labOrder->order_number }}
                        </div>
                    </div>
                </div>

                @if($labOrder->notes)
                    <div class="mt-4">
                        <div class="text-xs text-gray-500">Notes</div>
                        <div class="text-sm text-gray-800">{{ $labOrder->notes }}</div>
                    </div>
                @endif
            </div>

            {{-- Result Summary --}}
            <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 p-6 mb-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Result Summary</h3>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="rounded-xl border border-gray-200 p-4">
                        <div class="text-xs text-gray-500">Total Tests</div>
                        <div class="mt-1 text-lg font-semibold text-gray-900">{{ $totalTests }}</div>
                    </div>

                    <div class="rounded-xl border border-gray-200 p-4">
                        <div class="text-xs text-gray-500">Entered</div>
                        <div class="mt-1 text-lg font-semibold text-yellow-600">{{ $enteredCount }}</div>
                    </div>

                    <div class="rounded-xl border border-gray-200 p-4">
                        <div class="text-xs text-gray-500">Verified</div>
                        <div class="mt-1 text-lg font-semibold text-green-600">{{ $verifiedCount }}</div>
                    </div>

                    <div class="rounded-xl border border-gray-200 p-4">
                        <div class="text-xs text-gray-500">Abnormal Results</div>
                        <div class="mt-1 text-lg font-semibold {{ $abnormalCount > 0 ? 'text-red-600' : 'text-gray-900' }}">
                            {{ $abnormalCount }}
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <div class="flex items-center justify-between text-xs text-gray-500">
                        <span>Verification Progress</span>
                        <span>{{ $verifiedCount }}/{{ $totalTests }}</span>
                    </div>
                    <div class="mt-2 h-2 w-full overflow-hidden rounded-full bg-gray-100">
                        <div class="h-2 rounded-full bg-indigo-600"
                             style="width: {{ $totalTests > 0 ? ($verifiedCount / $totalTests) * 100 : 0 }}%"></div>
                    </div>
                </div>
            </div>

            {{-- Approval Section --}}
            <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 p-6 mb-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">Final Approval</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            The final report can be approved only after all tests are verified.
                        </p>
                    </div>

                    @if($labOrder->approved_at)
                        <span class="inline-flex rounded-full bg-green-50 px-3 py-1 text-xs font-medium text-green-700 ring-1 ring-green-200">
                            Approved
                        </span>
                    @endif
                </div>

                <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="rounded-xl border border-gray-200 p-4">
                        <div class="text-xs text-gray-500">Approval Status</div>
                        <div class="mt-1 text-sm font-semibold text-gray-900">
                            {{ $labOrder->approved_at ? 'Approved' : 'Pending Approval' }}
                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-200 p-4">
                        <div class="text-xs text-gray-500">Approved By</div>
                        <div class="mt-1 text-sm font-semibold text-gray-900">
                            {{ $labOrder->approver?->name ?? '-' }}
                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-200 p-4">
                        <div class="text-xs text-gray-500">Approved At</div>
                        <div class="mt-1 text-sm font-semibold text-gray-900">
                            {{ $labOrder->approved_at ? $labOrder->approved_at->format('Y-m-d h:i A') : '-' }}
                        </div>
                    </div>
                </div>

                @if(!$labOrder->approved_at)
                    <div class="mt-4 flex items-center justify-between rounded-xl border border-dashed border-gray-300 p-4">
                        <div class="text-sm text-gray-600">
                            @if($canApprove)
                                All tests are verified. This report is ready for final approval.
                            @else
                                This report cannot be approved yet. Make sure all tests are verified first.
                            @endif
                        </div>

                        <form method="POST" action="{{ route('lab-orders.approve', $labOrder) }}">
                            @csrf
                            @method('PATCH')

                            <button type="submit"
                                    class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 disabled:pointer-events-none disabled:opacity-50"
                                    @disabled(!$canApprove)>
                                Approve Report
                            </button>
                        </form>
                    </div>
                @endif
            </div>

            {{-- Selected Panels --}}
            <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 p-6 mb-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-3">Selected Panels</h3>

                <div class="space-y-2">
                    @forelse($labOrder->groups as $g)
                        <div class="flex items-center justify-between rounded-xl border border-gray-200 p-3">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $g->testGroup?->name ?? 'Panel' }}
                            </div>
                            <div class="text-sm text-gray-700">
                                Rs {{ number_format((float) $g->group_price_snapshot, 2) }}
                            </div>
                        </div>
                    @empty
                        <div class="text-sm text-gray-500">No panels.</div>
                    @endforelse
                </div>
            </div>

            {{-- Expanded Tests - Panel by Panel --}}
            <div class="space-y-6">
                @forelse($labOrder->groups as $group)
                    @php
                        $groupAbnormalCount = $group->tests->where('is_abnormal', true)->count();
                    @endphp

                    <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 p-6">
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
                                        <th class="py-2 pr-4">Test</th>
                                        <th class="py-2 pr-4">Unit</th>
                                        <th class="py-2 pr-4">Ref Range</th>
                                        <th class="py-2 pr-4">Status</th>
                                        <th class="py-2 pr-4">Result</th>
                                        <th class="py-2 pr-4">Abnormal</th>
                                        <th class="py-2 pr-4">Verified At</th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y divide-gray-100">
                                    @forelse($group->tests as $t)
                                        @php
                                            $abnormal = abnormalBadge($t);
                                        @endphp

                                        <tr class="{{ $t->is_abnormal ? 'bg-red-50/40' : '' }}">
                                            <td class="py-3 pr-4 font-medium text-gray-900">
                                                {{ $t->test_name }}
                                            </td>

                                            <td class="py-3 pr-4 text-gray-700">
                                                {{ $t->unit ?? '-' }}
                                            </td>

                                            <td class="py-3 pr-4 text-gray-700 whitespace-nowrap">
                                                {{ formatRefRange($t->ref_min, $t->ref_max) }}
                                            </td>

                                            <td class="py-3 pr-4">
                                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium ring-1 {{ testStatusBadge($t->status) }}">
                                                    {{ ucfirst($t->status) }}
                                                </span>
                                            </td>

                                            <td class="py-3 pr-4 text-gray-700">
                                                {{ $t->result_value ?? '-' }}
                                            </td>

                                            <td class="py-3 pr-4">
                                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium ring-1 {{ $abnormal['class'] }}">
                                                    {{ $abnormal['label'] }}
                                                </span>
                                            </td>

                                            <td class="py-3 pr-4 text-gray-700 whitespace-nowrap">
                                                {{ $t->verified_at ? $t->verified_at->format('Y-m-d h:i A') : '-' }}
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
                @empty
                    <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 p-6">
                        <div class="text-sm text-gray-500">No grouped tests found.</div>
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>