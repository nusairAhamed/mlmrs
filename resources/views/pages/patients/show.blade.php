<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Patient Profile</h2>
        <p class="text-sm text-gray-500 mt-0.5">{{ $patient->full_name }}</p>
    </x-slot>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-4">

        {{-- Actions above card --}}
        <div class="flex items-center justify-end gap-2">
            <a href="{{ route('patients.label', $patient) }}" target="_blank"
               class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-sm font-medium text-gray-700 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z"/>
                </svg>
                Print Label
            </a>
            <a href="{{ route('patients.edit', $patient) }}"
               class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-sm font-medium text-white transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/>
                </svg>
                Edit
            </a>
            <a href="{{ route('patients.index') }}"
               class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-sm font-medium text-gray-600 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                </svg>
                Back
            </a>
        </div>

        {{-- Profile card --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 flex items-center gap-5">
            <div class="flex items-center justify-center w-16 h-16 rounded-full bg-indigo-600 text-white text-2xl font-bold shrink-0">
                {{ strtoupper(substr($patient->full_name, 0, 1)) }}
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900">{{ $patient->full_name }}</h3>
                <p class="text-sm text-gray-500 font-mono">{{ $patient->patient_code }}</p>
                <span class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700 capitalize">
                    {{ $patient->gender }}
                </span>
            </div>
        </div>

        {{-- Details --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm divide-y divide-gray-50">

            <div class="flex items-center justify-between px-6 py-4">
                <span class="text-sm font-medium text-gray-500">Patient Code</span>
                <span class="text-sm font-mono text-gray-800">{{ $patient->patient_code }}</span>
            </div>

            <div class="flex items-center justify-between px-6 py-4">
                <span class="text-sm font-medium text-gray-500">Full Name</span>
                <span class="text-sm font-medium text-gray-800">{{ $patient->full_name }}</span>
            </div>

            <div class="flex items-center justify-between px-6 py-4">
                <span class="text-sm font-medium text-gray-500">Date of Birth</span>
                <span class="text-sm text-gray-800">
                    {{ \Carbon\Carbon::parse($patient->dob)->format('d M Y') }}
                    <span class="text-gray-400 text-xs ml-1">({{ \Carbon\Carbon::parse($patient->dob)->age }} yrs)</span>
                </span>
            </div>

            <div class="flex items-center justify-between px-6 py-4">
                <span class="text-sm font-medium text-gray-500">Gender</span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700 capitalize">
                    {{ $patient->gender }}
                </span>
            </div>

            <div class="flex items-center justify-between px-6 py-4">
                <span class="text-sm font-medium text-gray-500">Phone</span>
                <span class="text-sm text-gray-800">{{ $patient->phone ?: '—' }}</span>
            </div>

            <div class="flex items-center justify-between px-6 py-4">
                <span class="text-sm font-medium text-gray-500">Email</span>
                <span class="text-sm text-gray-800">{{ $patient->email ?: '—' }}</span>
            </div>

            <div class="flex items-center justify-between px-6 py-4">
                <span class="text-sm font-medium text-gray-500">Address</span>
                <span class="text-sm text-gray-800 text-right max-w-xs">{{ $patient->address ?: '—' }}</span>
            </div>

        </div>

    </div>
</x-app-layout>
