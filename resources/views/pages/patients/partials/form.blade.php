@php
    $isEdit = isset($patient);
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-5">

    {{-- Full Name --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
        <input type="text" name="full_name"
               value="{{ old('full_name', $patient->full_name ?? '') }}"
               class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
        @error('full_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Phone --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
        <input type="text" name="phone"
               value="{{ old('phone', $patient->phone ?? '') }}"
               class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
        @error('phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- DOB --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
        <input type="date" name="dob"
               value="{{ old('dob', isset($patient) ? \Carbon\Carbon::parse($patient->dob)->format('Y-m-d') : '') }}"
               class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
        @error('dob') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Gender --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
        <select name="gender"
                class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
            @php $g = old('gender', $patient->gender ?? ''); @endphp
            <option value="" disabled {{ $g === '' ? 'selected' : '' }}>Select</option>
            <option value="male"   {{ $g === 'male' ? 'selected' : '' }}>Male</option>
            <option value="female" {{ $g === 'female' ? 'selected' : '' }}>Female</option>
            <option value="other"  {{ $g === 'other' ? 'selected' : '' }}>Other</option>
        </select>
        @error('gender') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Email --}}
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Email (optional)</label>
        <input type="email" name="email"
               value="{{ old('email', $patient->email ?? '') }}"
               class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
        @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Address --}}
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Address (optional)</label>
        <textarea name="address" rows="3"
                  class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">{{ old('address', $patient->address ?? '') }}</textarea>
        @error('address') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

</div>