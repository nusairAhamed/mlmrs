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
            if (is_null($min) && is_null($max)) return '-';

            $format = function ($value) {
                if (is_null($value)) return '-';
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
    @endphp

    <div class="max-w-5xl mx-auto bg-white shadow-sm ring-1 ring-gray-200">
        <div class="border-b border-gray-200 px-8 py-6">
            <div class="flex items-start justify-between">
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

        <div class="px-8 py-6">
            <h3 class="mb-4 text-sm font-semibold text-gray-900">Test Results</h3>

            <div class="space-y-6">
                @foreach($labOrder->groups as $group)
                    <div>
                        <h4 class="mb-3 text-sm font-semibold text-gray-900">
                            {{ $group->testGroup?->name ?? 'Panel' }}
                        </h4>

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
                                    @foreach($group->tests as $t)
                                        @php $abnormal = abnormalLabel($t); @endphp
                                        <tr class="{{ $t->is_abnormal ? 'bg-red-50/30' : '' }}">
                                            <td class="px-4 py-3 font-medium text-gray-900">{{ $t->test_name }}</td>
                                            <td class="px-4 py-3 text-gray-900">{{ $t->result_value ?? '-' }}</td>
                                            <td class="px-4 py-3 text-gray-700">{{ $t->unit ?? '-' }}</td>
                                            <td class="px-4 py-3 text-gray-700">{{ formatRefRange($t->ref_min, $t->ref_max) }}</td>
                                            <td class="px-4 py-3 {{ $abnormal['class'] }}">{{ $abnormal['label'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="border-t border-gray-200 px-8 py-6">
            <div class="flex items-end justify-between">
                <div class="text-xs text-gray-500">
                    <p>This is a computer-generated laboratory report.</p>
                    <p>Please contact the laboratory for clarification if required.</p>
                </div>

                <div class="text-right">
                    <div class="text-sm text-gray-500">Approved By</div>
                    <div class="text-sm font-semibold text-gray-900">{{ $labOrder->approver?->name ?? '-' }}</div>
                    <div class="mt-1 text-xs text-gray-500">
                        {{ $labOrder->approved_at ? $labOrder->approved_at->format('Y-m-d h:i A') : '-' }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>