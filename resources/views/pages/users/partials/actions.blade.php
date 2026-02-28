<div class="flex gap-2">
    <a href="{{ route('users.edit', $user) }}"
       class="px-3 py-1 bg-blue-600 text-white rounded">
        Edit
    </a>

    <form method="POST"
          action="{{ route('users.destroy', $user) }}"
          onsubmit="return confirm('Delete this user?');">
        @csrf
        @method('DELETE')
        <button type="submit"
                class="px-3 py-1 bg-red-600 text-white rounded">
            Delete
        </button>
    </form>
</div>