<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Samples — {{ $labOrder->order_number }}
        </h2>
    </x-slot>

    @php
        $canManageSamples = in_array($labOrder->status, ['pending', 'sample_collected', 'sample_rejected'], true);
        $hasLockedSamples = $labOrder->samples->contains(fn($s) =>
            in_array($s->status, ['received', 'in_process', 'completed'], true)
        );
    @endphp

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Action buttons row --}}
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-2">
                    <a href="{{ route('lab-orders.index') }}"
                       class="rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition">
                        ← Lab Orders
                    </a>
                    <a href="{{ route('lab-orders.show', $labOrder) }}"
                       class="rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition">
                        View Order
                    </a>
                </div>

                <div class="flex items-center gap-2">
                    @if($labOrder->samples->isNotEmpty())
                        <a href="{{ route('lab-orders.samples.print-all', $labOrder) }}"
                           target="_blank"
                           class="rounded-lg border border-indigo-300 bg-white px-4 py-2 text-sm font-medium text-indigo-700 hover:bg-indigo-50 transition">
                            Print All Samples
                        </a>
                    @endif
                    @if($canManageSamples)
                        <a href="{{ route('lab-orders.samples.create', $labOrder) }}"
                           class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition
                                  {{ $hasLockedSamples ? 'opacity-50 pointer-events-none' : '' }}">
                            {{ $labOrder->samples->isEmpty() ? 'Generate Samples' : 'Add More Samples' }}
                        </a>
                    @endif
                </div>
            </div>

            @if($hasLockedSamples)
                <div class="mb-4 rounded-lg bg-yellow-50 p-4 text-yellow-700 ring-1 ring-yellow-200 text-sm">
                    Cannot add more samples — at least one sample is already received or in process.
                </div>
            @endif

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

            @if($errors->any())
                <div class="mb-4 rounded-lg bg-red-50 p-4 text-red-700 ring-1 ring-red-200 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            {{-- Order summary card --}}
            <div class="bg-white rounded-lg shadow-sm ring-1 ring-gray-200 p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <div class="text-xs text-gray-500">Patient</div>
                        <div class="text-sm font-semibold text-gray-900 mt-1">
                            {{ $labOrder->patient?->patient_code ?? '' }}{{ $labOrder->patient?->patient_code ? ' — ' : '' }}
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
                        <div class="text-xs text-gray-500">Samples</div>
                        <div class="text-sm font-semibold text-gray-900 mt-1">{{ $labOrder->samples->count() }}</div>
                    </div>
                </div>
            </div>

            {{-- Samples list card --}}
            <div class="bg-white rounded-lg shadow-sm ring-1 ring-gray-200 overflow-hidden">
                <div class="p-6">
                    <h3 class="text-sm font-semibold text-gray-900 mb-4">Generated Samples</h3>

                    <div class="space-y-3">
                        @forelse($labOrder->samples as $s)
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 rounded-lg border border-gray-200 p-4">
                                <div>
                                    <div class="text-sm font-semibold text-gray-900">
                                        {{ strtoupper($s->sample_type) }}
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        Code: <span class="font-mono text-gray-800">{{ $s->sample_code }}</span>
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                                        Status:
                                        @php
                                            $sMap = [
                                                'collected'  => ['bg-blue-50',   'text-blue-700',   'ring-blue-200',   'Collected'],
                                                'received'   => ['bg-indigo-50', 'text-indigo-700', 'ring-indigo-200', 'Received'],
                                                'in_process' => ['bg-yellow-50', 'text-yellow-700', 'ring-yellow-200', 'In Process'],
                                                'completed'  => ['bg-green-50',  'text-green-700',  'ring-green-200',  'Completed'],
                                                'rejected'   => ['bg-red-50',    'text-red-700',    'ring-red-200',    'Rejected'],
                                            ];
                                            [$sbg, $stxt, $srng, $slbl] = $sMap[$s->status] ?? ['bg-gray-50', 'text-gray-600', 'ring-gray-200', ucfirst($s->status)];
                                        @endphp
                                        <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium ring-1 {{ $sbg }} {{ $stxt }} {{ $srng }}">
                                            {{ $slbl }}
                                        </span>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2">
                                    <a href="{{ route('lab-samples.label', $s) }}"
                                       target="_blank"
                                       class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition">
                                        Print Label
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="text-sm text-gray-500">
                                No samples created yet.
                                @if($canManageSamples)
                                    Use <span class="font-semibold">Generate Samples</span> above.
                                @endif
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
