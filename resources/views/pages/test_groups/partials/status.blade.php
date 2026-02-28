<span class="inline-flex items-center px-2 py-1 rounded-lg text-sm text-white
    {{ $group->status === 'active'
        ? 'bg-green-600'
        : 'bg-red-600' }}">
    {{ ucfirst($group->status) }}
</span>