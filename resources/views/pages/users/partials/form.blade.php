@php
    $isEdit = isset($user);
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-5">

    {{-- Name --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
        <input type="text" name="name"
               value="{{ old('name', $user->name ?? '') }}"
               class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm
                      focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Email --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <input type="email" name="email"
               value="{{ old('email', $user->email ?? '') }}"
               class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm
                      focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
        @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Role --}}
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
        <select name="role_id"
                class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm
                       focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
            <option value="" disabled {{ old('role_id', $user->role_id ?? '') == '' ? 'selected' : '' }}>
                Select
            </option>

            @foreach($roles as $role)
                <option value="{{ $role->id }}"
                    @selected(old('role_id', $user->role_id ?? '') == $role->id)>
                    {{ $role->name }}
                </option>
            @endforeach
        </select>
        @error('role_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Password --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Password {{ $isEdit ? '(optional)' : '' }}
        </label>
        <input type="password" name="password"
               class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm
                      focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
        @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror

        @if($isEdit)
            <p class="mt-1 text-xs text-gray-500">Leave blank to keep the current password.</p>
        @endif
    </div>

    {{-- Confirm Password --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
        <input type="password" name="password_confirmation"
               class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm
                      focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
    </div>

</div>