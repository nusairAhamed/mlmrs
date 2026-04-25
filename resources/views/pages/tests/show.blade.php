<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Test Details</h2>
        <p class="text-sm text-gray-500 mt-0.5">{{ $test->name }}</p>
    </x-slot>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-4">

        {{-- Actions above card --}}
        <div class="flex items-center justify-end gap-2">
            <a href="{{ route('tests.edit', $test) }}"
               class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-sm font-medium text-white transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/>
                </svg>
                Edit
            </a>
            <a href="{{ route('tests.index') }}"
               class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-sm font-medium text-gray-600 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                </svg>
                Back
            </a>
        </div>

        {{-- Details card --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm divide-y divide-gray-50">

            <div class="flex items-center justify-between px-6 py-4">
                <span class="text-sm font-medium text-gray-500">Test Name</span>
                <span class="text-sm font-semibold text-gray-800">{{ $test->name }}</span>
            </div>

            <div class="flex items-center justify-between px-6 py-4">
                <span class="text-sm font-medium text-gray-500">Unit</span>
                <span class="text-sm text-gray-800">{{ $test->unit ?: '—' }}</span>
            </div>

            <div class="flex items-center justify-between px-6 py-4">
                <span class="text-sm font-medium text-gray-500">Data Type</span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    {{ $test->data_type === 'numeric' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700' }}">
                    {{ ucfirst($test->data_type) }}
                </span>
            </div>

            <div class="flex items-center justify-between px-6 py-4">
                <span class="text-sm font-medium text-gray-500">Status</span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    {{ $test->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-600' }}">
                    {{ ucfirst($test->status) }}
                </span>
            </div>

            <div class="flex items-center justify-between px-6 py-4">
                <span class="text-sm font-medium text-gray-500">Test Groups</span>
                <div class="flex flex-wrap gap-1.5 justify-end">
                    @forelse($test->testGroups as $group)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700">
                            {{ $group->name }}
                        </span>
                    @empty
                        <span class="text-sm text-gray-400">—</span>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- Reference ranges --}}
        @if($test->data_type === 'numeric' && $test->ranges->isNotEmpty())
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-50">
                <h3 class="text-sm font-semibold text-gray-700">Reference Ranges</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wide px-6 py-3">Gender</th>
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wide px-6 py-3">Age Min</th>
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wide px-6 py-3">Age Max</th>
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wide px-6 py-3">Ref Min</th>
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wide px-6 py-3">Ref Max</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($test->ranges as $range)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3 text-gray-700 capitalize">{{ $range->gender }}</td>
                            <td class="px-6 py-3 text-gray-700">{{ $range->age_min ?? '—' }}</td>
                            <td class="px-6 py-3 text-gray-700">{{ $range->age_max ?? '—' }}</td>
                            <td class="px-6 py-3 text-gray-700">{{ $range->ref_min ?? '—' }}</td>
                            <td class="px-6 py-3 text-gray-700">{{ $range->ref_max ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

    </div>
</x-app-layout>
