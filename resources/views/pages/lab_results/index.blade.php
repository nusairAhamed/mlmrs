<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800">
                Enter Results — {{ $labOrder->order_number }}
            </h2>

            <div class="flex items-center gap-2">
                <a href="{{ route('lab-orders.show', $labOrder) }}"
                   class="rounded-xl bg-white px-4 py-2 text-sm font-semibold text-gray-700 ring-1 ring-gray-200 hover:bg-gray-50">
                    Back to Order
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

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

            @if($errors->any())
                <div class="mb-4 rounded-xl bg-red-50 p-4 text-red-700 ring-1 ring-red-200">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <div class="text-xs text-gray-500">Patient</div>
                        <div class="text-sm font-semibold text-gray-900">
                            {{ $labOrder->patient?->patient_code ?? '' }}{{ $labOrder->patient?->patient_code ? ' — ' : '' }}
                            {{ $labOrder->patient?->full_name ?? '-' }}
                        </div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500">Order Status</div>
                        <div class="mt-1">
                            @include('pages.lab_orders.partials.status', ['order' => $labOrder])
                        </div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500">Total Tests</div>
                        <div class="text-sm font-semibold text-gray-900">{{ $labOrder->tests->count() }}</div>
                    </div>
                </div>
            </div>

            @foreach($labOrder->groups as $group)
                <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 p-6 mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold text-gray-900">
                            {{ $group->testGroup?->name ?? 'Panel' }}
                        </h3>
                        <div class="text-xs text-gray-500">
                            Tests: {{ $group->tests->count() }}
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="text-left text-gray-600">
                                <tr>
                                    <th class="py-2">Test</th>
                                    <th class="py-2">Unit</th>
                                    <th class="py-2">Ref</th>
                                    <th class="py-2">Result</th>
                                    <th class="py-2">Status</th>
                                    <th class="py-2">Abnormal</th>
                                    <th class="py-2 text-right">Actions</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-100">
                                @forelse($group->tests as $t)
                                    <tr>
                                        <td class="py-2 font-medium text-gray-900">{{ $t->test_name }}</td>
                                        <td class="py-2 text-gray-700">{{ $t->unit ?? '-' }}</td>
                                        <td class="py-2 text-gray-700">
                                            @if(!is_null($t->ref_min) || !is_null($t->ref_max))
                                                {{ $t->ref_min ?? '-' }} - {{ $t->ref_max ?? '-' }}
                                            @else
                                                -
                                            @endif
                                        </td>

                                        <td class="py-2">
                                            <form method="POST" action="{{ route('lab-order-tests.result.update', $t) }}" class="flex items-center gap-2">
                                                @csrf
                                                @method('PATCH')

                                                <input name="result_value"
                                                       value="{{ old('result_value', $t->result_value) }}"
                                                       class="w-36 rounded-lg border border-gray-200 px-3 py-1.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
                                                       placeholder="Enter result"
                                                       @disabled($labOrder->status === 'approved')>

                                                <button type="submit"
                                                        class="rounded-lg bg-gray-900 px-3 py-1.5 text-xs font-semibold text-white hover:bg-black
                                                               {{ $labOrder->status === 'approved' ? 'opacity-50 pointer-events-none' : '' }}">
                                                    Save
                                                </button>
                                            </form>
                                        </td>

                                        <td class="py-2 text-gray-700">{{ $t->status }}</td>

                                        <td class="py-2">
                                            @if($t->is_abnormal)
                                                <span class="inline-flex rounded-full bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-red-200">Yes</span>
                                            @else
                                                <span class="inline-flex rounded-full bg-gray-50 px-2 py-1 text-xs font-medium text-gray-700 ring-1 ring-gray-200">No</span>
                                            @endif
                                        </td>

                                        <td class="py-2 text-right">
                                            <form method="POST" action="{{ route('lab-order-tests.verify', $t) }}" class="inline">
                                                @csrf
                                                @method('PATCH')

                                                <button type="submit"
                                                        class="rounded-lg px-3 py-1.5 text-xs font-semibold text-green-700 ring-1 ring-green-200 hover:bg-green-50
                                                               {{ $t->status === 'verified' || $labOrder->status === 'approved' ? 'opacity-50 pointer-events-none' : '' }}">
                                                    Verify
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="py-3 text-gray-500">No tests in this panel.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach

        </div>
    </div>
</x-app-layout>