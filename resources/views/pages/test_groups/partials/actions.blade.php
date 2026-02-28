



<div class="flex justify-start gap-2">

    <a href="{{ route('test-groups.edit', $group) }}"
       class="px-3 py-1.5 rounded-lg bg-indigo-600 text-white text-sm">
        <x-heroicon-o-pencil class="w-4 h-4" data-tippy-content="Edit" />
    </a>

    <form action="{{ route('test-groups.destroy', $group) }}"
          method="POST"
          onsubmit="return confirm('Delete this test group?')">

        @csrf
        @method('DELETE')

        <button type="submit"
                class="px-3 py-1.5 rounded-lg bg-red-600 text-white text-sm">
            <x-heroicon-o-trash class="w-4 h-4" />
        </button>
    </form>

</div>