
<x-app-layout>
     <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Patient
        </h2>
    </x-slot>
<div class="py-6">
<div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

    <div class="mb-6">
       
        <p class="text-sm text-gray-500 mt-1">Code: <span class="font-medium text-gray-700">{{ $patient->patient_code }}</span></p>
    </div>

    <div class="bg-white rounded-lg shadow-sm ring-1 ring-gray-200 p-6">
        <form method="POST" action="{{ route('patients.update', $patient) }}" class="space-y-6">
            @csrf
            @method('PUT')

            @include('pages.patients.partials.form')

            <div class="flex items-center justify-end gap-2 pt-2">
                <a href="{{ route('patients.index') }}"
                   class="rounded-lg bg-white px-4 py-2 text-sm font-medium text-gray-700 ring-1 ring-gray-200 hover:bg-gray-50 transition">
                    Cancel
                </a>

                <button type="submit"
                        class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                    Update
                </button>
            </div>
        </form>
    </div>

</div>
</div>

</x-app-layout>