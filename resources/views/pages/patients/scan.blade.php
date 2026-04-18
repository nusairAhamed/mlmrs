<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Scan Patient Card</h2>
    </x-slot>

    <div class="min-h-[70vh] flex items-center justify-center px-4">
        <div class="w-full max-w-md">

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">

                <div class="h-1.5 bg-indigo-600"></div>

                <div class="px-8 py-10">

                    <div class="flex flex-col items-center mb-8">
                        <div class="flex items-center justify-center w-16 h-16 rounded-full bg-indigo-50 mb-4">
                            <svg class="w-8 h-8 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900">Scan Patient Card</h3>
                        <p class="mt-1 text-sm text-gray-500 text-center">
                            Scan the patient's ID barcode.<br>
                            You'll be taken straight to create a lab order.
                        </p>
                    </div>

                    @if ($errors->has('patient_code'))
                        <div class="mb-6 flex items-start gap-3 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
                            <svg class="w-5 h-5 shrink-0 mt-0.5 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                            </svg>
                            <span>{{ $errors->first('patient_code') }}</span>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('patients.scan.submit') }}" id="scan-form">
                        @csrf

                        <div class="relative">
                            <input
                                type="text"
                                id="patient_code"
                                name="patient_code"
                                autofocus
                                autocomplete="off"
                                placeholder="Waiting for scan…"
                                value="{{ old('patient_code') }}"
                                class="w-full rounded-lg border {{ $errors->has('patient_code') ? 'border-red-400 bg-red-50' : 'border-gray-300 bg-gray-50' }}
                                       px-4 py-3 text-center text-lg font-mono tracking-widest text-gray-800
                                       focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                                       placeholder-gray-400 transition"
                            >
                        </div>

                        <button type="submit"
                                class="mt-4 w-full rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white
                                       hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                            Submit Manually
                        </button>
                    </form>

                    <p class="mt-6 text-center text-xs text-gray-400">
                        Patient code format: <span class="font-mono">PAT-YYYY-NNNNN</span>
                    </p>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
    <script>
        const input = document.getElementById('patient_code');
        const form  = document.getElementById('scan-form');

        input.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                if (input.value.trim() !== '') {
                    form.submit();
                }
            }
        });

        window.addEventListener('load', () => input.focus());
    </script>
    @endpush
</x-app-layout>
