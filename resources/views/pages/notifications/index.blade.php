<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Notifications
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

            <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Channel</label>
                    <select id="filter-channel"
                            class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All</option>
                        <option value="sms">SMS</option>
                        <option value="email">Email</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select id="filter-status"
                            class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All</option>
                        <option value="pending">Pending</option>
                        <option value="sent">Sent</option>
                        <option value="failed">Failed</option>
                    </select>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 overflow-hidden">
                <div class="p-4 sm:p-6 relative">
                    <table id="notifications-table" class="min-w-full text-left"></table>

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
            const channelFilter = document.getElementById('filter-channel');
            const statusFilter = document.getElementById('filter-status');

            const table = new DataTable('#notifications-table', {
                processing: true,
                serverSide: true,
                pagingType: "simple_numbers",
                ajax: {
                    url: "{{ route('notifications.index') }}",
                    data: function (d) {
                        d.channel = channelFilter.value;
                        d.status = statusFilter.value;
                    }
                },
                language: {
                    processing: ''
                },
                order: [[6, 'desc']],
                columns: [
                    { data: 'patient_name', name: 'patient.full_name', title: 'Patient' },
                    { data: 'order_number', name: 'labOrder.order_number', title: 'Order Number' },
                    { data: 'channel_badge', name: 'channel', title: 'Channel', orderable: false, searchable: false },
                    { data: 'status_badge', name: 'status', title: 'Status', orderable: false, searchable: false },
                    { data: 'message', name: 'message', title: 'Message' },
                    { data: 'sent_at', name: 'sent_at', title: 'Sent At' },
                    { data: 'created_at', name: 'created_at', title: 'Created At' },
                    { data: 'action', name: 'action', title: 'Action', orderable:false, searchable:false }
                ]
            });

            table.on('processing', function (e, settings, processing) {
                loadingEl.classList.toggle('hidden', !processing);
            });

            channelFilter.addEventListener('change', function () {
                table.ajax.reload();
            });

            statusFilter.addEventListener('change', function () {
                table.ajax.reload();
            });
        });
    </script>
</x-app-layout>