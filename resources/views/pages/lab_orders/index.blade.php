<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800">Lab Orders</h2>

            <a href="{{ route('lab-orders.create') }}"
               class="inline-flex items-center rounded-xl bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-black">
                + Create Lab Order
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            
        <div class="flex items-center justify-end mb-6">
            <a href="{{ route('lab-orders.create') }}"
               class="px-4 py-2 bg-black text-white rounded-lg">
                + Create Test Group
            </a>
        </div>

            @if(session('success'))
                <div class="mb-4 rounded-xl bg-green-50 p-4 text-green-700 ring-1 ring-green-200">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 rounded-xl bg-red-50 p-4 text-red-700 ring-1 ring-red-200">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 p-6 mb-6">
                @include('pages.lab_orders.partials.filter')
            </div>

            <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 p-6">
                <div class="overflow-x-auto">
                    <table id="lab-orders-table" class="min-w-full w-full"></table>
                </div>
            </div>

        </div>
    </div>

    {{-- DataTables JS --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const table = $('#lab-orders-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('lab-orders.index') }}",
                    data: function (d) {
                        d.order_number = document.getElementById('filter_order_number').value;
                        d.status = document.getElementById('filter_status').value;
                    }
                },
                columns: [
                    { data: 'order_number', name: 'order_number', title: 'Order #' },
                    { data: 'patient_name', name: 'patient.name', title: 'Patient' },
                    { data: 'total_amount', name: 'total_amount', title: 'Total' },
                    { data: 'status_badge', name: 'status', orderable: false, searchable: false, title: 'Status' },
                    { data: 'created_at', name: 'created_at', title: 'Created' },
                    { data: 'action', name: 'action', orderable: false, searchable: false, title: 'Actions' },
                ],
                order: [[4, 'desc']],
            });

            // Filter actions
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