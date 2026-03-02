<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800">
                Generate Samples — {{ $labOrder->order_number }}
            </h2>

            <div class="flex items-center gap-2">
                <a href="{{ route('lab-orders.samples.index', $labOrder) }}"
                   class="rounded-xl bg-white px-4 py-2 text-sm font-semibold text-gray-700 ring-1 ring-gray-200 hover:bg-gray-50">
                    Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if($errors->any())
                <div class="mb-4 p-3 rounded bg-red-100 text-red-800">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('lab-orders.samples.store', $labOrder) }}"
                  class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 p-6 space-y-4">
                @csrf

                <div class="text-sm text-gray-600">
                    Select sample type(s) and quantity. System will generate a barcode per physical specimen.
                </div>

                <div id="rows" class="space-y-3"></div>

                <div class="flex items-center gap-2">
                    <button type="button" id="add-row"
                            class="rounded-xl bg-white px-4 py-2 text-sm font-semibold text-gray-700 ring-1 ring-gray-200 hover:bg-gray-50">
                        + Add Sample Type
                    </button>

                    <button type="submit"
                            class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                        Generate
                    </button>
                </div>

                <p class="text-xs text-gray-500">
                    Tip: Do not add the same type twice (e.g., Blood + Blood). Use Qty instead.
                </p>
            </form>
        </div>
    </div>

    <template id="row-template">
        <div class="row-item rounded-2xl border border-gray-200 p-4">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                <div class="md:col-span-7">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Sample Type</label>
                    <select class="sample-type w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
                        <option value="">-- Select --</option>
                        @foreach($sampleTypes as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-3">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Qty</label>
                    <input type="number" min="1" max="10" value="1"
                           class="qty w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
                </div>

                <div class="md:col-span-2 flex md:justify-end">
                    <button type="button"
                            class="remove-row rounded-xl px-3 py-2 text-sm font-semibold text-red-700 ring-1 ring-red-200 hover:bg-red-50">
                        Remove
                    </button>
                </div>
            </div>
        </div>
    </template>

    <script>
        (function () {
            const rows = document.getElementById('rows');
            const tpl = document.getElementById('row-template');
            const addBtn = document.getElementById('add-row');

            function renumberInputs() {
                const items = Array.from(rows.querySelectorAll('.row-item'));
                items.forEach((item, idx) => {
                    const type = item.querySelector('.sample-type');
                    const qty  = item.querySelector('.qty');

                    type.name = `samples[${idx}][sample_type]`;
                    qty.name  = `samples[${idx}][qty]`;
                });
            }

            function addRow(prefillType = '', prefillQty = 1) {
                const node = tpl.content.cloneNode(true);
                const wrap = node.querySelector('.row-item');
                const type = wrap.querySelector('.sample-type');
                const qty  = wrap.querySelector('.qty');

                type.value = prefillType;
                qty.value  = prefillQty;

                wrap.querySelector('.remove-row').addEventListener('click', () => {
                    wrap.remove();
                    renumberInputs();
                });

                rows.appendChild(node);
                renumberInputs();
            }

            addBtn.addEventListener('click', () => addRow());

            // Start with one row
            addRow('blood', 1);
        })();
    </script>
</x-app-layout>