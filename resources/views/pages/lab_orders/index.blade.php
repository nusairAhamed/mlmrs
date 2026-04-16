<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Lab Orders</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="flex items-center justify-end mb-6">
                <a href="{{ route('lab-orders.create') }}"
                   class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition">
                    + Create Lab Order
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

            {{-- Scanned patient banner --}}
            @if($scannedPatient)
            <div class="mb-4 flex items-center justify-between rounded-lg bg-indigo-50 border border-indigo-200 px-5 py-3">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-indigo-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                    </svg>
                    <div>
                        <p class="text-sm font-semibold text-indigo-800">{{ $scannedPatient->full_name }}</p>
                        <p class="text-xs text-indigo-600">{{ $scannedPatient->patient_code }}
                            &middot; {{ ucfirst($scannedPatient->gender) }}
                            &middot; DOB: {{ \Carbon\Carbon::parse($scannedPatient->dob)->format('d M Y') }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('lab-orders.create', ['patient_id' => $scannedPatient->id]) }}"
                       class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-indigo-700 transition">
                        + New Order
                    </a>
                    <a href="{{ route('lab-orders.index') }}"
                       class="inline-flex items-center rounded-lg bg-white border border-indigo-200 px-3 py-1.5 text-xs font-medium text-indigo-600 hover:bg-indigo-50 transition">
                        Clear
                    </a>
                </div>
            </div>
            @endif

            <div class="bg-white rounded-lg shadow-sm ring-1 ring-gray-200 p-6 mb-6">
                @include('pages.lab_orders.partials.filter')
            </div>

            <div class="bg-white rounded-lg shadow-sm ring-1 ring-gray-200 p-6">
                <div class="overflow-x-auto">
                    <table id="lab-orders-table" class="min-w-full w-full"></table>
                </div>
            </div>

        </div>
    </div>

    {{-- DataTables JS --}}
    <script>
        const scannedPatientId = @json($scannedPatient?->id);

        document.addEventListener('DOMContentLoaded', function () {
            const table = $('#lab-orders-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('lab-orders.index') }}",
                    data: function (d) {
                        d.order_number = document.getElementById('filter_order_number').value;
                        d.status       = document.getElementById('filter_status').value;
                        if (scannedPatientId) {
                            d.patient_id = scannedPatientId;
                        }
                    }
                },
                columns: [
                    { data: 'order_number', name: 'order_number', title: 'Order #' },
                    { data: 'patient_name', name: 'patient.name', title: 'Patient', orderable: false },
                    { data: 'status_badge', name: 'status',       title: 'Status',  orderable: false, searchable: false },
                    { data: 'created_at',   name: 'created_at',   title: 'Created' },
                    { data: 'action',       name: 'action',       title: 'Actions', orderable: false, searchable: false },
                ],
                order: [[3, 'desc']],
            });

            document.getElementById('btn_apply_filters').addEventListener('click', function () {
                table.ajax.reload();
            });

            document.getElementById('btn_clear_filters').addEventListener('click', function () {
                document.getElementById('filter_order_number').value = '';
                document.getElementById('filter_status').value = '';
                table.ajax.reload();
            });
        });
    </script>
</x-app-layout>