<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
    <input type="text" name="name"
           value="{{ old('name', $category->name ?? '') }}"
           class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
    @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
</div>




<div class="flex items-center justify-end gap-3 pt-2">
    <a href="{{ route('test-categories.index') }}"
       class="rounded-xl bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200">
        Cancel
    </a>
    <button type="submit"
            class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
        Save
    </button>
</div>