<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Test Groups
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

        <div class="flex items-center justify-end mb-6">
            <a href="{{ route('test-groups.create') }}"
               class="px-4 py-2 bg-black text-white rounded-lg">
                + Create Test Group
            </a>
        </div>

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

        {{-- Filter --}}
        @include('pages.test_groups.partials.filter')

        <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 overflow-hidden">
            <div class="p-4 sm:p-6 relative">

                <table id="test-groups-table" class="min-w-full text-left"></table>

                {{-- Loading Overlay --}}
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

    
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const loadingEl = document.getElementById('loading');

            const table = new DataTable('#test-groups-table', {
                processing: true,
                serverSide: true,
                pagingType: "simple_numbers",

                ajax: {
                    url: "{{ route('test-groups.index') }}",
                    data: function (d) {
                        const form = document.getElementById('test-groups-filter-form');
                        const formData = new FormData(form);

                        formData.forEach((value, key) => {
                            d[key] = value;
                        });
                    }
                },

                language: {
                    processing: ''
                },

                columns: [
                    { data: 'name', name: 'name', title: 'Name' },
                    { data: 'category_name', name: 'category.name', title: 'Category' },
                    { data: 'price', name: 'price', title: 'Price' },
                    { data: 'status_badge', name: 'status', orderable: false, searchable: false, title: 'Status' },
                    { data: 'action', name: 'action', orderable: false, searchable: false, title: 'Actions' },
                ],

                order: [[0, 'asc']]
            });

            // overlay toggle
            table.on('processing', function (e, settings, processing) {
                if (processing) loadingEl.classList.remove('hidden');
                else loadingEl.classList.add('hidden');
            });

            // Apply filter
            document.getElementById('test-groups-filter-form')
                .addEventListener('submit', function (e) {
                    e.preventDefault();
                    table.ajax.reload();
                });

            // Reset filter
            document.getElementById('reset-test-groups-filter')
                .addEventListener('click', function () {
                    document.getElementById('test-groups-filter-form').reset();
                    table.ajax.reload();
                });
        });
    </script>

</x-app-layout>