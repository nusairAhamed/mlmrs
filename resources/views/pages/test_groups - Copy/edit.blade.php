<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Edit Test Group</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 p-6">
                <form method="POST" action="{{ route('test-groups.update', $testGroup) }}" class="space-y-6">
                    @csrf
                    @method('PUT')
                    @include('pages.test_groups.partials.form', ['testGroup' => $testGroup])
                    <div class="flex items-center justify-end gap-2">
                        <a href="{{ route('test-groups.index') }}" class="rounded-xl px-4 py-2 text-sm font-semibold ring-1 ring-gray-300 hover:bg-gray-50">
                            Cancel
                        </a>
                        <button class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>