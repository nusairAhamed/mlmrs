<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800">
                Samples — {{ $labOrder->order_number }}
            </h2>

            <div class="flex items-center gap-2">
                <a href="{{ route('lab-orders.show', $labOrder) }}"
                   class="rounded-xl bg-white px-4 py-2 text-sm font-semibold text-gray-700 ring-1 ring-gray-200 hover:bg-gray-50">
                    Back to Order
                </a>

 @php
    $canManageSamples = in_array($labOrder->status, ['pending','in_progress'], true);
    $hasLockedSamples = $labOrder->samples->contains(fn($s) =>
        in_array($s->status, ['received','in_process','completed'], true)
    );
@endphp

@if($canManageSamples)

    @if($labOrder->samples->isEmpty())
        {{-- First time generation --}}
        <a href="{{ route('lab-orders.samples.create', $labOrder) }}"
           class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
            Generate Samples
        </a>
    @else
        {{-- Add more samples --}}
        <a href="{{ route('lab-orders.samples.create', $labOrder) }}"
           class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700
                  {{ $hasLockedSamples ? 'opacity-50 pointer-events-none' : '' }}">
            Add More Samples
        </a>
    @endif

@endif

@if($hasLockedSamples)
    <div class="text-xs text-gray-500 mt-2">
        Cannot add more samples because at least one sample is already received or in process.
    </div>
@endif
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-3 rounded bg-green-100 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-3 rounded bg-red-100 text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 p-3 rounded bg-red-100 text-red-800">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <div class="text-xs text-gray-500">Patient</div>
                        <div class="text-sm font-semibold text-gray-900">
                            {{ $labOrder->patient?->patient_code ?? '' }}{{ $labOrder->patient?->patient_code ? ' — ' : '' }}
                            {{ $labOrder->patient?->full_name ?? $labOrder->patient?->name ?? '-' }}
                        </div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500">Status</div>
                        <div class="text-sm font-semibold text-gray-900">{{ $labOrder->status }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500">Samples</div>
                        <div class="text-sm font-semibold text-gray-900">{{ $labOrder->samples->count() }}</div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 overflow-hidden">
                <div class="p-6">
                    <h3 class="text-sm font-semibold text-gray-900 mb-4">Generated Samples</h3>

                    <div class="space-y-3">
                        @forelse($labOrder->samples as $s)
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 rounded-xl border border-gray-200 p-4">
                                <div>
                                    <div class="text-sm font-semibold text-gray-900">
                                        {{ strtoupper($s->sample_type) }}
                                    </div>

                                    <div class="text-xs text-gray-500 mt-1">
                                        Code: <span class="font-mono text-gray-800">{{ $s->sample_code }}</span>
                                    </div>

                                    <div class="text-xs text-gray-500 mt-1">
                                        Status: <span class="font-medium text-gray-700">{{ $s->status }}</span>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2">
                                    <a href="{{ route('lab-samples.label', $s) }}"
                                       target="_blank"
                                       class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                                        Print Label
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="text-sm text-gray-500">
                                No samples created yet.
                                @if(in_array($labOrder->status, ['pending','in_progress']))
                                    Use <span class="font-semibold">Generate Samples</span>.
                                @endif
                            </div>
                        @endforelse
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>