<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit User
        </h2>
    </x-slot>
    <div class="max-w-3xl mx-auto p-6">
      

        @if($errors->any())
            <div class="mb-4 p-3 rounded bg-red-100 text-red-800">
                <ul class="list-disc ml-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('users.update', $user) }}" class="bg-white p-6 rounded shadow space-y-4">
            @csrf
            @method('PUT')

            @include('pages.users.partials.form')

            

            <div class="flex gap-2">
                <button class="px-4 py-2 bg-black text-white rounded" type="submit">Update</button>
                <a class="px-4 py-2 border rounded" href="{{ route('users.index') }}">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>