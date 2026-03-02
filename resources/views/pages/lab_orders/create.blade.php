<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Create Lab Order</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 p-6">
                <form method="POST" action="{{ route('lab-orders.store') }}">
                    @csrf
                    @include('pages.lab_orders.partials.form', [
                        'labOrder' => null,
                        'patients' => $patients,
                        'selectedGroupIds' => [],
                    ])

                    <div class="mt-6 flex items-center gap-2">
                        <button type="submit"
                                class="rounded-xl bg-gray-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-black">
                            Save Order
                        </button>
                        <a href="{{ route('lab-orders.index') }}"
                           class="rounded-xl bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 ring-1 ring-gray-200 hover:bg-gray-50">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>