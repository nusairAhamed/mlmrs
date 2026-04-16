<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Create Test
        </h2>
    </x-slot>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="bg-white rounded-lg shadow-sm ring-1 ring-gray-200 p-6">

            @if ($errors->any())
                <div class="mb-4 rounded-lg bg-red-50 p-4 text-red-700 ring-1 ring-red-200">
                    <ul class="list-disc pl-5 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('tests.store') }}" class="space-y-6">
                @csrf

                @include('pages.tests.partials.form')

                <div class="flex items-center justify-end gap-2 mt-6">
                    <a href="{{ route('tests.index') }}"
                       class="rounded-lg bg-white px-4 py-2 text-sm font-medium text-gray-700 ring-1 ring-gray-200 hover:bg-gray-50 transition">
                        Cancel
                    </a>

                    <button type="submit"
                            class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition">
                        Save
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>