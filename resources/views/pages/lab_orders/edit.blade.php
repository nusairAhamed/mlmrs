<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Lab Order — {{ $labOrder->order_number }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            {{-- Flash Messages --}}
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

            {{-- Warning if panels are locked --}}
            @if($hasAnyProgress)
                <div class="mb-4 rounded-lg bg-amber-50 p-4 text-amber-800 ring-1 ring-amber-200">
                    Panels are locked because results have already been entered or verified for this order.
                </div>
            @endif

            <div class="bg-white rounded-lg shadow-sm ring-1 ring-gray-200 overflow-hidden">
                <div class="p-6">

                    <form method="POST" action="{{ route('lab-orders.update', $labOrder) }}">
                        @csrf
                        @method('PATCH')

                        {{-- Main form (patient + notes + panels cards) --}}
                        @include('pages.lab_orders.partials.form', [
  'labOrder' => $labOrder,
  'patients' => $patients,
  'groups' => $groups,
  'selectedGroupIds' => $selectedGroupIds,
  'allowPanelEdit' => !$hasAnyProgress,
])

                        {{-- Hidden status — preserve current value, not user-editable --}}
                        <input type="hidden" name="status" value="{{ $labOrder->status }}">

                        {{-- Actions --}}
                        <div class="mt-6 flex items-center justify-end gap-2">
                            <a href="{{ route('lab-orders.index') }}"
                               class="rounded-lg px-4 py-2 text-sm font-medium text-gray-700 ring-1 ring-gray-200 hover:bg-gray-50">
                                Cancel
                            </a>

                            <button type="submit"
                                    class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                                Update Order
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>