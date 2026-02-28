<x-app-layout>
     <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Create User
        </h2>
    </x-slot>
    <div class="max-w-3xl mx-auto p-6">
       

      
        <form method="POST" action="{{ route('users.store') }}" class="bg-white p-6 rounded shadow space-y-4">
            @csrf

            @include('pages.users.partials.form', ['roles' => $roles])

            <div class="flex gap-2">
                <button class="px-4 py-2 bg-black text-white rounded" type="submit">Create</button>
                <a class="px-4 py-2 border rounded" href="{{ route('users.index') }}">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>