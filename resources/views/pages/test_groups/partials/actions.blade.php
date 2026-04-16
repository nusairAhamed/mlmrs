<div class="flex items-center gap-2">
    <a href="{{ route('test-groups.show', $group) }}"
       class="px-3 py-1.5 rounded-lg bg-gray-100 text-gray-600 text-sm hover:bg-gray-200 transition"
       title="View">
        <x-heroicon-s-eye class="w-4 h-4" />
    </a>

    <a href="{{ route('test-groups.edit', $group) }}"
       class="px-3 py-1.5 rounded-lg bg-indigo-600 text-white text-sm hover:bg-indigo-700 transition"
       title="Edit">
        <x-heroicon-s-pencil-square class="w-4 h-4" />
    </a>

    <form action="{{ route('test-groups.destroy', $group) }}"
          method="POST"
          onsubmit="return confirm('Delete this test group?')">
        @csrf
        @method('DELETE')
        <button type="submit"
                class="px-3 py-1.5 rounded-lg bg-red-600 text-white text-sm hover:bg-red-700 transition"
                title="Delete">
            <x-heroicon-s-trash class="w-4 h-4" />
        </button>
    </form>
</div>
