<div class="flex items-center gap-2">

    <a href="{{ route('tests.edit', $test) }}"
       class="p-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700"
       title="Edit">
        ✏️
    </a>

    <form action="{{ route('tests.destroy', $test) }}"
          method="POST"
          onsubmit="return confirm('Delete this test?')">
        @csrf
        @method('DELETE')

        <button type="submit"
                class="p-2 rounded-lg bg-red-600 text-white hover:bg-red-700"
                title="Delete">
            🗑
        </button>
    </form>

</div>