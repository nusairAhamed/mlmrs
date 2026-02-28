<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">New Test Category</h2></x-slot>
    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 p-6">
                <form method="POST" action="{{ route('test-categories.store') }}" class="space-y-5">
                    @csrf
                    @include('pages.test_categories.partials.form', ['category' => null])
                </form>
            </div>
        </div>
    </div>
</x-app-layout>