<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Test Categories
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

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

            <div class="mb-6 flex items-center justify-end">
              
                <a href="{{ route('test-categories.create') }}"
                   class="inline-flex items-center rounded-xl bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                    + New Category
                </a>
            </div>

       

            <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 overflow-hidden">
                <div class="p-4 sm:p-6 relative">
                    <table id="categories-table" class="min-w-full text-left"></table>

                    {{-- Loading Overlay (blocks table interactions) --}}
                    <div id="loading"
                        class="hidden absolute inset-0 bg-white/70 backdrop-blur-sm flex items-center justify-center z-20">
                        <div class="flex items-center gap-3">
                            <svg class="h-6 w-6 animate-spin text-indigo-600"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25"
                                        cx="12"
                                        cy="12"
                                        r="10"
                                        stroke="currentColor"
                                        stroke-width="4"></circle>
                                <path class="opacity-75"
                                    fill="currentColor"
                                    d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-700">Loading...</span>
                        </div>
                    </div>
                    
                </div>
            </div>

            

        </div>
    </div>

        <script>
        document.addEventListener('DOMContentLoaded', function () {
            const loadingEl = document.getElementById('loading');

            const table = new DataTable('#categories-table', {
                processing: true,
                serverSide: true,
                pagingType: "simple_numbers",
                ajax: {
                    url: "{{ route('test-categories.index') }}"
                },
                language: {
                    processing: ''
                },
                columns: [
                    { data: 'name', name: 'name', title: 'Name' },
                    { data: 'action', name: 'action', orderable: false, searchable: false, title: 'Actions' },
                ]
            });

            table.on('processing', function (e, settings, processing) {
                loadingEl.classList.toggle('hidden', !processing);
            });
        });
    </script>
</x-app-layout>