<div class="flex items-center gap-2">
    <a href="{{ route('tests.edit', $test) }}"
       class="px-3 py-1.5 rounded-lg bg-indigo-600 text-white text-sm hover:bg-indigo-700 transition"
       title="Edit">
        <x-heroicon-s-pencil-square class="w-4 h-4" />
    </a>

    <form action="{{ route('tests.destroy', $test) }}"
          method="POST"
          onsubmit="return confirm('Delete this test?')">
        @csrf
        @method('DELETE')
        <button type="submit"
                class="px-3 py-1.5 rounded-lg bg-red-600 text-white text-sm hover:bg-red-700 transition"
                title="Delete">
            <x-heroicon-s-trash class="w-4 h-4" />
        </button>
    </form>
</div>
