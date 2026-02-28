<div class="flex gap-2">
    <a href="{{ route('test-categories.edit', $category) }}"
       class="px-3 py-1 bg-blue-600 text-white rounded">
        Edit
    </a>

    <form method="POST"
          action="{{ route('test-categories.destroy', $category) }}"
          onsubmit="return confirm('Delete this category?')">
        @csrf
        @method('DELETE')
        <button type="submit"
                class="px-3 py-1 bg-red-600 text-white rounded">
            Delete
        </button>
    </form>
</div>