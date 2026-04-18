<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Scan Sample</h2>
    </x-slot>

    <div class="min-h-[70vh] flex items-center justify-center px-4">
        <div class="w-full max-w-md">

            {{-- Card --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">

                {{-- Top accent --}}
                <div class="h-1.5 bg-indigo-600"></div>

                <div class="px-8 py-10">

                    {{-- Icon + heading --}}
                    <div class="flex flex-col items-center mb-8">
                        <div class="flex items-center justify-center w-16 h-16 rounded-full bg-indigo-50 mb-4">
                            <svg class="w-8 h-8 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M6.75 6.75h.75v.75h-.75v-.75zM6.75 17.25h.75v.75h-.75v-.75zM16.5 6.75h.75v.75h-.75v-.75zM13.5 13.5h.75v.75h-.75v-.75zM13.5 19.5h.75v.75h-.75v-.75zM19.5 13.5h.75v.75h-.75v-.75zM19.5 19.5h.75v.75h-.75v-.75zM16.5 16.5h.75v.75h-.75v-.75z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900">Scan Sample Barcode</h3>
                        <p class="mt-1 text-sm text-gray-500 text-center">
                            Point the barcode reader at the sample label.<br>
                            The form submits automatically after each scan.
                        </p>
                    </div>

                    {{-- Error message --}}
                    @if ($errors->has('sample_code'))
                        <div class="mb-6 flex items-start gap-3 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
                            <svg class="w-5 h-5 shrink-0 mt-0.5 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                            </svg>
                            <span>{{ $errors->first('sample_code') }}</span>
                        </div>
                    @endif

                    {{-- Scan form --}}
                    <form method="POST" action="{{ route('samples.scan.submit') }}" id="scan-form">
                        @csrf

                        <div class="relative">
                            <input
                                type="text"
                                id="sample_code"
                                name="sample_code"
                                autofocus
                                autocomplete="off"
                                placeholder="Waiting for scan…"
                                value="{{ old('sample_code') }}"
                                class="w-full rounded-lg border {{ $errors->has('sample_code') ? 'border-red-400 bg-red-50' : 'border-gray-300 bg-gray-50' }}
                                       px-4 py-3 text-center text-lg font-mono tracking-widest text-gray-800
                                       focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                                       placeholder-gray-400 transition"
                            >
                        </div>

                        {{-- Manual submit for edge cases --}}
                        <button type="submit"
                                class="mt-4 w-full rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white
                                       hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                            Submit Manually
                        </button>
                    </form>

                    {{-- Hint --}}
                    <p class="mt-6 text-center text-xs text-gray-400">
                        Sample format: <span class="font-mono">SMP-YYYYMMDD-XXXXXX-XX</span>
                    </p>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
    <script>
        const input = document.getElementById('sample_code');
        const form  = document.getElementById('scan-form');

        // Auto-submit when the barcode reader sends Enter
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                if (input.value.trim() !== '') {
                    form.submit();
                }
            }
        });

        // Re-focus the input after error so the next scan goes straight in
        window.addEventListener('load', () => input.focus());
    </script>
    @endpush
</x-app-layout>
