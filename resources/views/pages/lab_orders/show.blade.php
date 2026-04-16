<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Lab Order: {{ $labOrder->order_number }}</h2>
    </x-slot>

    @php
        $totalTests = $labOrder->tests->count();
        $enteredCount = $labOrder->tests->where('status', 'entered')->count();
        $verifiedCount = $labOrder->tests->where('status', 'verified')->count();
        $abnormalCount = $labOrder->tests->where('is_abnormal', true)->count();

        $allVerified = $totalTests > 0 && $verifiedCount === $totalTests;
        $canApprove  = in_array($labOrder->status, ['pending_review', 'on_hold']) && $allVerified && is_null($labOrder->approved_at);
        $canHold     = $labOrder->status === 'pending_review' && $allVerified;

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

            {{-- Action buttons --}}
            <div class="flex items-center justify-end gap-2 mb-4">
                @if($labOrder->status === 'pending')
                    <a href="{{ route('lab-orders.edit', $labOrder) }}"
                       class="inline-flex items-center gap-1.5 rounded-lg bg-white px-4 py-2 text-sm font-medium text-gray-700 ring-1 ring-gray-200 hover:bg-gray-50 transition">
                        Edit
                    </a>
                @endif

                @if(in_array(Auth::user()->role->name ?? '', ['Admin', 'Receptionist']))
                    <a href="{{ route('lab-orders.samples.index', $labOrder) }}"
                       class="inline-flex items-center gap-1.5 rounded-lg bg-white px-4 py-2 text-sm font-medium text-gray-700 ring-1 ring-gray-200 hover:bg-gray-50 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15M14.25 3.104c.251.023.501.05.75.082M19.5 7.336A2.25 2.25 0 0121 9.5v9.75A2.25 2.25 0 0118.75 21.5h-13.5A2.25 2.25 0 013 19.25V9.5a2.25 2.25 0 011.5-2.164" />
                        </svg>
                        Samples
                    </a>
                @endif

                @if($labOrder->status === 'approved')
                    <a href="{{ route('lab-reports.show', $labOrder) }}"
                       class="inline-flex items-center gap-1.5 rounded-lg bg-white px-4 py-2 text-sm font-medium text-gray-700 ring-1 ring-gray-200 hover:bg-gray-50 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        View Report
                    </a>
                    <a href="{{ route('lab-reports.pdf', $labOrder) }}" target="_blank"
                       class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        Download PDF
                    </a>
                @endif

                <a href="{{ route('lab-orders.index') }}"
                   class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition">
                    ← Back
                </a>
            </div>

            @if(session('success'))
                <div class="mb-4 rounded-lg bg-green-50 p-4 text-green-700 ring-1 ring-green-200">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 rounded-lg bg-red-50 p-4 text-red-700 ring-1 ring-red-200">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Order Summary --}}
            <div class="bg-white rounded-lg shadow-sm ring-1 ring-gray-200 p-6 mb-6">
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
            <div class="bg-white rounded-lg shadow-sm ring-1 ring-gray-200 p-6 mb-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Result Summary</h3>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="rounded-lg border border-gray-200 p-4">
                        <div class="text-xs text-gray-500">Total Tests</div>
                        <div class="mt-1 text-lg font-semibold text-gray-900">{{ $totalTests }}</div>
                    </div>

                    <div class="rounded-lg border border-gray-200 p-4">
                        <div class="text-xs text-gray-500">Entered</div>
                        <div class="mt-1 text-lg font-semibold text-yellow-600">{{ $enteredCount }}</div>
                    </div>

                    <div class="rounded-lg border border-gray-200 p-4">
                        <div class="text-xs text-gray-500">Verified</div>
                        <div class="mt-1 text-lg font-semibold text-green-600">{{ $verifiedCount }}</div>
                    </div>

                    <div class="rounded-lg border border-gray-200 p-4">
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
            <div class="bg-white rounded-lg shadow-sm ring-1 ring-gray-200 p-6 mb-6">
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
                    <div class="rounded-lg border border-gray-200 p-4">
                        <div class="text-xs text-gray-500">Approval Status</div>
                        <div class="mt-1 text-sm font-semibold text-gray-900">
                            {{ $labOrder->approved_at ? 'Approved' : 'Pending Approval' }}
                        </div>
                    </div>

                    <div class="rounded-lg border border-gray-200 p-4">
                        <div class="text-xs text-gray-500">Approved By</div>
                        <div class="mt-1 text-sm font-semibold text-gray-900">
                            {{ $labOrder->approver?->name ?? '-' }}
                        </div>
                    </div>

                    <div class="rounded-lg border border-gray-200 p-4">
                        <div class="text-xs text-gray-500">Approved At</div>
                        <div class="mt-1 text-sm font-semibold text-gray-900">
                            {{ $labOrder->approved_at ? $labOrder->approved_at->format('d M Y, h:i A') : '-' }}
                        </div>
                    </div>
                </div>

                @if(!$labOrder->approved_at)
                    <div class="mt-4 rounded-lg border border-dashed border-gray-300 p-4">

                        {{-- On Hold notice --}}
                        @if($labOrder->status === 'on_hold')
                            <div class="mb-4 rounded-lg bg-purple-50 p-3 text-sm text-purple-700 ring-1 ring-purple-200">
                                This report is on hold. The patient has <strong>not</strong> been notified. Release it when ready to deliver results in person or send notification.
                            </div>
                        @endif

                        <div class="flex items-center justify-between gap-3 flex-wrap">
                            <div class="text-sm text-gray-600">
                                @if($labOrder->status === 'on_hold')
                                    Report is on hold — approve to release and notify the patient.
                                @elseif($canApprove)
                                    All tests are verified. This report is ready for final approval.
                                @else
                                    All tests must be verified before approving or holding the report.
                                @endif
                            </div>

                            <div class="flex items-center gap-2">

                                {{-- Hold button — only from pending_review --}}
                                @if($canHold)
                                    <form method="POST" action="{{ route('lab-orders.hold', $labOrder) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                onclick="return confirm('Place this report on hold? The patient will NOT be notified. Use this for sensitive results requiring in-person counselling.')"
                                                class="rounded-lg bg-purple-600 px-4 py-2 text-sm font-semibold text-white hover:bg-purple-700">
                                            Hold Report
                                        </button>
                                    </form>
                                @endif

                                {{-- Approve button --}}
                                <form method="POST" action="{{ route('lab-orders.approve', $labOrder) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 disabled:pointer-events-none disabled:opacity-50"
                                            @disabled(!$canApprove)>
                                        {{ $labOrder->status === 'on_hold' ? 'Release & Notify Patient' : 'Approve & Notify Patient' }}
                                    </button>
                                </form>

                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Selected Panels --}}
            <div class="bg-white rounded-lg shadow-sm ring-1 ring-gray-200 p-6 mb-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-3">Selected Panels</h3>

                <div class="space-y-2">
                    @forelse($labOrder->groups as $g)
                        <div class="flex items-center justify-between rounded-lg border border-gray-200 p-3">
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

                    <div class="bg-white rounded-lg shadow-sm ring-1 ring-gray-200 p-6">
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
                                        <th class="py-2 pr-4">Verified By</th>
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

                                            <td class="py-3 pr-4 whitespace-nowrap">
                                                @if($t->verifiedBy)
                                                    <div class="text-sm font-medium text-gray-800">{{ $t->verifiedBy->name }}</div>
                                                    <div class="text-xs text-gray-400">{{ $t->verified_at?->format('d M Y, h:i A') }}</div>
                                                @else
                                                    <span class="text-gray-400">—</span>
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
                @empty
                    <div class="bg-white rounded-lg shadow-sm ring-1 ring-gray-200 p-6">
                        <div class="text-sm text-gray-500">No grouped tests found.</div>
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>