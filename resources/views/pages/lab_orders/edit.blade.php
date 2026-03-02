<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Lab Order — {{ $labOrder->order_number }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            {{-- Flash Messages --}}
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

            {{-- Warning if panels are locked --}}
            @if($hasAnyProgress)
                <div class="mb-4 p-3 rounded bg-yellow-100 text-yellow-900">
                    Panels are locked because results have already been entered or verified for this order.
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 overflow-hidden">
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

                        {{-- Status --}}
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>

                            @php
                                $current = old('status', $labOrder->status);
                            @endphp

                            <select name="status"
                                    class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
                                <option value="pending" @selected($current === 'pending')>Pending</option>
                                <option value="in_progress" @selected($current === 'in_progress')>In Progress</option>
                                <option value="completed" @selected($current === 'completed')>Completed</option>
                                <option value="approved" @selected($current === 'approved')>Approved</option>
                            </select>

                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror

                            <p class="mt-2 text-xs text-gray-500">
                                Tip: Panels can be edited only while the order is pending and no results have been entered.
                            </p>
                        </div>

                        {{-- Actions --}}
                        <div class="mt-8 flex items-center justify-end gap-2">
                            <a href="{{ route('lab-orders.index') }}"
                               class="rounded-xl px-4 py-2 text-sm font-medium text-gray-700 ring-1 ring-gray-200 hover:bg-gray-50">
                                Cancel
                            </a>

                            <button type="submit"
                                    class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                                Update Order
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>