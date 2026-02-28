<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Tests
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

        <div class="flex items-center justify-end mb-6">
            <a href="{{ route('tests.create') }}"
               class="px-4 py-2 bg-black text-white rounded-lg">
                + Create Test
            </a>
        </div>

        @include('pages.tests.partials.filter')

        <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 overflow-hidden">
            <div class="p-6 relative">

                <table id="tests-table" class="min-w-full text-left"></table>

                <div id="loading"
                     class="hidden absolute inset-0 bg-white/70 backdrop-blur-sm flex items-center justify-center">
                    <span class="text-sm font-medium text-gray-700">Loading...</span>
                </div>

            </div>
        </div>
    </div>

 
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const loadingEl = document.getElementById('loading');

            const table = new DataTable('#tests-table', {
                processing: true,
                serverSide: true,

                ajax: {
                    url: "{{ route('tests.index') }}",
                    data: function (d) {
                        const form = document.getElementById('tests-filter-form');
                        const formData = new FormData(form);

                        formData.forEach((value, key) => {
                            d[key] = value;
                        });
                    }
                },

                columns: [
                    { data: 'name', name: 'name', title: 'Name' },
                    { data: 'unit', name: 'unit', title: 'Unit' },
                    { data: 'data_type', name: 'data_type', title: 'Type' },
                    { data: 'sort_order', name: 'sort_order', title: 'Order' },
                    { data: 'status_badge', name: 'status', orderable: false, searchable: false, title: 'Status' },
                    { data: 'action', name: 'action', orderable: false, searchable: false, title: 'Actions' },
                ],

                order: [[0, 'asc']]
            });

            table.on('processing', function (e, settings, processing) {
                if (processing) loadingEl.classList.remove('hidden');
                else loadingEl.classList.add('hidden');
            });

            document.getElementById('tests-filter-form')
                .addEventListener('submit', function (e) {
                    e.preventDefault();
                    table.ajax.reload();
                });

            document.getElementById('reset-tests-filter')
                .addEventListener('click', function () {
                    document.getElementById('tests-filter-form').reset();
                    table.ajax.reload();
                });
        });
    </script>
 
</x-app-layout>