
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Create Patient
        </h2>
    </x-slot>
<div class="max-w-3xl mx-auto p-6">

    <div class="mb-6">
      
        <p class="text-sm text-gray-500 mt-1">Patient code will be generated automatically.</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 p-6">
        <form method="POST" action="{{ route('patients.store') }}" class="space-y-6">
            @csrf

            @include('pages.patients.partials.form')

            <div class="flex items-center justify-end gap-2 pt-2">
                <a href="{{ route('patients.index') }}"
                   class="rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>

                <button type="submit"
                        class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                    Save
                </button>
            </div>
        </form>
    </div>

</div>
</x-app-layout>