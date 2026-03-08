<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Report - {{ $labOrder->order_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 py-8">
    @php
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
                return ['label' => 'Normal', 'class' => 'text-green-700 font-medium'];
            }

            if (
                ($test->test?->data_type ?? 'text') === 'numeric' &&
                !is_null($test->result_value) &&
                is_numeric($test->result_value)
            ) {
                $value = (float) $test->result_value;

                if (!is_null($test->ref_min) && $value < (float) $test->ref_min) {
                    return ['label' => 'Low', 'class' => 'text-blue-700 font-semibold'];
                }

                if (!is_null($test->ref_max) && $value > (float) $test->ref_max) {
                    return ['label' => 'High', 'class' => 'text-red-700 font-semibold'];
                }
            }

            return ['label' => 'Abnormal', 'class' => 'text-red-700 font-semibold'];
        }

        $publicReportUrl = ($labOrder->status === 'approved' && $labOrder->qrToken)
            ? route('public-reports.show', $labOrder->qrToken->token)
            : null;
    @endphp

    <div class="max-w-5xl mx-auto">
        <div class="mb-4 flex items-center justify-between print:hidden">
            <a href="{{ route('lab-orders.show', $labOrder) }}"
               class="rounded-xl bg-white px-4 py-2 text-sm font-semibold text-gray-700 ring-1 ring-gray-200 hover:bg-gray-50">
                Back to Order
            </a>

            <button onclick="window.print()"
                    class="rounded-xl bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-black">
                Print Report
            </button>
        </div>

        <div class="bg-white shadow-sm ring-1 ring-gray-200">
            {{-- Header --}}
            <div class="border-b border-gray-200 px-8 py-6">
                <div class="flex items-start justify-between gap-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Your Laboratory Name</h1>
                        <p class="text-sm text-gray-600">Laboratory Address Line</p>
                        <p class="text-sm text-gray-600">Phone: 0XX-XXXXXXX | Email: lab@example.com</p>
                    </div>

                    <div class="text-right">
                        <h2 class="text-lg font-semibold text-gray-900">Laboratory Report</h2>
                        <p class="text-sm text-gray-600">Report No: {{ $labOrder->order_number }}</p>
                        <p class="text-sm text-gray-600">
                            Issue Date:
                            {{ $labOrder->approved_at ? $labOrder->approved_at->format('Y-m-d h:i A') : '-' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Patient Information --}}
            <div class="border-b border-gray-200 px-8 py-6">
                <h3 class="mb-4 text-sm font-semibold text-gray-900">Patient Information</h3>

                <div class="grid grid-cols-2 gap-4 md:grid-cols-4 text-sm">
                    <div>
                        <div class="text-gray-500">Patient Code</div>
                        <div class="font-medium text-gray-900">{{ $patient?->patient_code ?? '-' }}</div>
                    </div>

                    <div>
                        <div class="text-gray-500">Patient Name</div>
                        <div class="font-medium text-gray-900">{{ $patient?->full_name ?? '-' }}</div>
                    </div>

                    <div>
                        <div class="text-gray-500">Age</div>
                        <div class="font-medium text-gray-900">{{ $age ?? '-' }}</div>
                    </div>

                    <div>
                        <div class="text-gray-500">Gender</div>
                        <div class="font-medium text-gray-900">
                            {{ !empty($patient?->gender) ? ucfirst($patient->gender) : '-' }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- QR / Public Report Section --}}
            @if($publicReportUrl)
                <div class="border-b border-gray-200 px-8 py-6">
                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex items-center gap-4">
                                <div class="shrink-0 rounded-xl bg-white p-3 ring-1 ring-gray-200">
                                    {!! QrCode::size(110)->margin(1)->generate($publicReportUrl) !!}
                                </div>

                                <div>
                                    <div class="text-sm font-semibold text-gray-900">Public Report Access</div>
                                    <div class="mt-1 text-sm text-gray-600">
                                        Scan this QR code to open the report online.
                                    </div>
                                    <div class="mt-2 text-sm font-medium text-gray-900 break-all">
                                        {{ $publicReportUrl }}
                                    </div>
                                </div>
                            </div>

                            <div class="text-xs text-gray-500 sm:text-right">
                                Available only for approved reports
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Results --}}
            <div class="px-8 py-6">
                <h3 class="mb-4 text-sm font-semibold text-gray-900">Test Results</h3>

                <div class="space-y-6">
                    @forelse($labOrder->groups as $group)
                        <div>
                            <div class="mb-3">
                                <h4 class="text-sm font-semibold text-gray-900">
                                    {{ $group->testGroup?->name ?? 'Panel' }}
                                </h4>
                            </div>

                            <div class="overflow-hidden rounded-xl ring-1 ring-gray-200">
                                <table class="min-w-full text-sm">
                                    <thead class="bg-gray-50 text-left text-gray-600">
                                        <tr>
                                            <th class="px-4 py-3">Test</th>
                                            <th class="px-4 py-3">Result</th>
                                            <th class="px-4 py-3">Unit</th>
                                            <th class="px-4 py-3">Reference Range</th>
                                            <th class="px-4 py-3">Flag</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @forelse($group->tests as $t)
                                            @php
                                                $abnormal = abnormalLabel($t);
                                            @endphp

                                            <tr class="{{ $t->is_abnormal ? 'bg-red-50/30' : '' }}">
                                                <td class="px-4 py-3 font-medium text-gray-900">
                                                    {{ $t->test_name }}
                                                </td>

                                                <td class="px-4 py-3 text-gray-900">
                                                    {{ $t->result_value ?? '-' }}
                                                </td>

                                                <td class="px-4 py-3 text-gray-700">
                                                    {{ $t->unit ?? '-' }}
                                                </td>

                                                <td class="px-4 py-3 text-gray-700">
                                                    {{ formatRefRange($t->ref_min, $t->ref_max) }}
                                                </td>

                                                <td class="px-4 py-3 {{ $abnormal['class'] }}">
                                                    {{ $abnormal['label'] }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="px-4 py-3 text-gray-500">No tests in this panel.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @empty
                        <div class="text-sm text-gray-500">No grouped results found.</div>
                    @endforelse
                </div>
            </div>

            {{-- Footer --}}
            <div class="border-t border-gray-200 px-8 py-6">
                <div class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
                    <div class="text-xs text-gray-500">
                        <p>This is a computer-generated laboratory report.</p>
                        <p>Please contact the laboratory for clarification if required.</p>
                    </div>

                    <div class="flex items-end gap-6">
                        @if($publicReportUrl)
                            <div class="text-center">
                                <div class="inline-block rounded-xl bg-white p-2 ring-1 ring-gray-200">
                                    {!! QrCode::size(90)->margin(1)->generate($publicReportUrl) !!}
                                </div>
                                <div class="mt-2 text-xs text-gray-500">Scan to verify report</div>
                            </div>
                        @endif

                        <div class="text-right">
                            <div class="text-sm text-gray-500">Approved By</div>
                            <div class="text-sm font-semibold text-gray-900">
                                {{ $labOrder->approver?->name ?? '-' }}
                            </div>
                            <div class="mt-1 text-xs text-gray-500">
                                {{ $labOrder->approved_at ? $labOrder->approved_at->format('Y-m-d h:i A') : '-' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            body {
                background: white !important;
                padding: 0 !important;
            }

            .print\:hidden {
                display: none !important;
            }

            .max-w-5xl {
                max-width: 100% !important;
                margin: 0 !important;
            }

            .shadow-sm,
            .ring-1 {
                box-shadow: none !important;
            }
        }
    </style>
</body>
</html>