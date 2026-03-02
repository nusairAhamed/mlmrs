<div class="flex items-center justify-end gap-2">

    <a href="{{ route('lab-orders.show', $order) }}"
       class="rounded-lg px-3 py-1.5 text-xs font-semibold ring-1 ring-gray-300 hover:bg-gray-50">
        View
    </a>

    <a href="{{ route('lab-orders.samples.index', $order) }}"
       class="rounded-lg px-3 py-1.5 text-xs font-semibold text-indigo-700 ring-1 ring-indigo-200 hover:bg-indigo-50">
        Samples
    </a>

    @if($order->status === 'pending')
        <a href="{{ route('lab-orders.edit', $order) }}"
           class="rounded-lg px-3 py-1.5 text-xs font-semibold ring-1 ring-gray-300 hover:bg-gray-50">
            Edit
        </a>

        <form action="{{ route('lab-orders.destroy', $order) }}" method="POST"
              onsubmit="return confirm('Delete this order?');">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="rounded-lg px-3 py-1.5 text-xs font-semibold text-red-700 ring-1 ring-red-200 hover:bg-red-50">
                Delete
            </button>
        </form>
    @endif
</div>