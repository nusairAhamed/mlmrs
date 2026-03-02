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

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <div class="text-xs text-gray-500">Patient</div>
                        <div class="text-sm font-semibold text-gray-900">{{ $labOrder->patient?->full_name  ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500">Status</div>
                        <div class="mt-1">@include('pages.lab_orders.partials.status', ['order' => $labOrder])</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500">Total Amount</div>
                        <div class="text-sm font-semibold text-gray-900">Rs {{ number_format((float)$labOrder->total_amount, 2) }}</div>
                    </div>
                </div>

                @if($labOrder->notes)
                    <div class="mt-4">
                        <div class="text-xs text-gray-500">Notes</div>
                        <div class="text-sm text-gray-800">{{ $labOrder->notes }}</div>
                    </div>
                @endif
            </div>

            <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 p-6 mb-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-3">Selected Panels</h3>

                <div class="space-y-2">
                    @forelse($labOrder->groups as $g)
                        <div class="flex items-center justify-between rounded-xl border border-gray-200 p-3">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $g->testGroup?->name ?? 'Panel' }}
                            </div>
                            <div class="text-sm text-gray-700">
                                Rs {{ number_format((float)$g->group_price_snapshot, 2) }}
                            </div>
                        </div>
                    @empty
                        <div class="text-sm text-gray-500">No panels.</div>
                    @endforelse
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 p-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-3">Expanded Tests</h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="text-left text-gray-600">
                            <tr>
                                <th class="py-2">Test</th>
                                <th class="py-2">Unit</th>
                                <th class="py-2">Status</th>
                                <th class="py-2">Result</th>
                                <th class="py-2">Abnormal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($labOrder->tests as $t)
                                <tr>
                                    <td class="py-2 font-medium text-gray-900">{{ $t->test_name }}</td>
                                    <td class="py-2 text-gray-700">{{ $t->unit ?? '-' }}</td>
                                    <td class="py-2 text-gray-700">{{ $t->status }}</td>
                                    <td class="py-2 text-gray-700">{{ $t->result_value ?? '-' }}</td>
                                    <td class="py-2">
                                        @if($t->is_abnormal)
                                            <span class="inline-flex rounded-full bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-red-200">Yes</span>
                                        @else
                                            <span class="inline-flex rounded-full bg-gray-50 px-2 py-1 text-xs font-medium text-gray-700 ring-1 ring-gray-200">No</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="py-3 text-gray-500">No tests expanded.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>