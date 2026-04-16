<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Test Group Details
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Action buttons --}}
            <div class="flex items-center justify-between mb-6">
                <a href="{{ route('test-groups.index') }}"
                   class="rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition">
                    ← Back
                </a>
                <a href="{{ route('test-groups.edit', $testGroup) }}"
                   class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition">
                    <x-heroicon-s-pencil-square class="w-4 h-4" />
                    Edit
                </a>
            </div>

            {{-- Details card --}}
            <div class="bg-white rounded-lg shadow-sm ring-1 ring-gray-200 p-6 mb-4">
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Name</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900">{{ $testGroup->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Category</p>
                        <p class="mt-1 text-sm text-gray-700">{{ $testGroup->category?->name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Price</p>
                        <p class="mt-1 text-sm text-gray-700">LKR {{ number_format($testGroup->price, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Status</p>
                        <div class="mt-1">
                            @include('pages.test_groups.partials.status', ['group' => $testGroup])
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tests card --}}
            <div class="bg-white rounded-lg shadow-sm ring-1 ring-gray-200 overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h3 class="text-sm font-semibold text-gray-800">Included Tests</h3>
                    <span class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-medium text-indigo-700 ring-1 ring-indigo-200">
                        {{ $testGroup->tests->count() }} tests
                    </span>
                </div>

                @if($testGroup->tests->isEmpty())
                    <div class="px-6 py-8 text-center text-sm text-gray-400">No tests assigned to this group.</div>
                @else
                    <ul class="divide-y divide-gray-100">
                        @foreach($testGroup->tests as $test)
                            <li class="flex items-center justify-between px-6 py-3">
                                <div>
                                    <p class="text-sm font-medium text-gray-800">{{ $test->name }}</p>
                                    @if($test->unit)
                                        <p class="text-xs text-gray-400 mt-0.5">Unit: {{ $test->unit }}</p>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-gray-400 capitalize">{{ $test->sample_type }}</span>
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                                        {{ $test->data_type === 'numeric' ? 'bg-blue-50 text-blue-600 ring-1 ring-blue-200' : 'bg-purple-50 text-purple-600 ring-1 ring-purple-200' }}">
                                        {{ ucfirst($test->data_type) }}
                                    </span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
