<div class="flex justify-end gap-2">
    <a href="{{ route('patients.edit', $row->id) }}"
       class="px-3 py-1.5 rounded-lg bg-indigo-600 text-white text-sm">
        Edit
    </a>

    <form action="{{ route('patients.destroy', $row->id) }}" method="POST"
          onsubmit="return confirm('Delete this patient?')">
        @csrf
        @method('DELETE')
        <button type="submit"
                class="px-3 py-1.5 rounded-lg bg-red-600 text-white text-sm">
            Delete
        </button>
    </form>
</div>