<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Users
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

        <div class="flex items-center justify-end mb-6">
            <a href="{{ route('users.create') }}"
               class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition">
                + Create User
            </a>
        </div>

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

        @include('pages.users.partials.filter')

        <div class="bg-white rounded-lg shadow-sm ring-1 ring-gray-200 overflow-hidden">
            <div class="p-4 sm:p-6 relative">
                <table id="users-table" class="min-w-full text-left"></table>

                <div id="loading"
                     class="hidden absolute inset-0 bg-white/70 backdrop-blur-sm flex items-center justify-center z-20">
                    <div class="flex items-center gap-3">
                        <svg class="h-6 w-6 animate-spin text-indigo-600"
                             xmlns="http://www.w3.org/2000/svg"
                             fill="none"
                             viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                  d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                        <span class="text-sm font-medium text-gray-700">Loading...</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loadingEl = document.getElementById('loading');

            const table = new DataTable('#users-table', {
                processing: true,
                serverSide: true,
                pagingType: "simple_numbers",
                ajax: {
                    url: "{{ route('users.index') }}",
                    data: function(d) {
                        const form = document.getElementById('filter-form');
                        const formData = new FormData(form);
                        formData.forEach((value, key) => { d[key] = value; });
                    }
                },
                language: { processing: '' },
                columns: [
                    { data: 'name',   name: 'name',      title: 'Name' },
                    { data: 'email',  name: 'email',     title: 'Email' },
                    { data: 'role',   name: 'role.name', title: 'Role', defaultContent: '-' },
                    { data: 'action', name: 'action',    title: 'Actions', orderable: false, searchable: false }
                ]
            });

            table.on('processing', function(e, settings, processing) {
                loadingEl.classList.toggle('hidden', !processing);
            });

            document.getElementById('filter-form').addEventListener('submit', function(e) {
                e.preventDefault();
                table.ajax.reload();
            });

            document.getElementById('reset-filter').addEventListener('click', function() {
                document.getElementById('filter-form').reset();
                table.ajax.reload();
            });
        });
    </script>
</x-app-layout>
