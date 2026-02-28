<span class="px-3 py-1.5 rounded-lg text-sm text-white
    {{ $test->status === 'active' ? 'bg-green-600' : 'bg-red-600' }}">
    {{ ucfirst($test->status) }}
</span>