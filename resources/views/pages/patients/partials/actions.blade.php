<div class="flex items-center gap-2">
    <a href="{{ route('patients.label', $row->id) }}" target="_blank"
       class="px-3 py-1.5 rounded-lg bg-gray-100 text-gray-700 text-sm border border-gray-200 hover:bg-gray-200 transition"
       title="Print Label">
        <x-heroicon-s-printer class="w-4 h-4" />
    </a>

    <a href="{{ route('patients.edit', $row->id) }}"
       class="px-3 py-1.5 rounded-lg bg-indigo-600 text-white text-sm hover:bg-indigo-700 transition"
       title="Edit">
        <x-heroicon-s-pencil-square class="w-4 h-4" />
    </a>

    <form action="{{ route('patients.destroy', $row->id) }}" method="POST"
          onsubmit="return confirm('Delete this patient?')">
        @csrf
        @method('DELETE')
        <button type="submit"
                class="px-3 py-1.5 rounded-lg bg-red-600 text-white text-sm hover:bg-red-700 transition"
                title="Delete">
            <x-heroicon-s-trash class="w-4 h-4" />
        </button>
    </form>
</div>
