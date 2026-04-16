@php
    $map = [
        'active'   => ['bg-green-50', 'text-green-700', 'ring-green-200', 'Active'],
        'inactive' => ['bg-red-50',   'text-red-700',   'ring-red-200',   'Inactive'],
    ];
    [$bg, $text, $ring, $label] = $map[$group->status] ?? ['bg-gray-50', 'text-gray-700', 'ring-gray-200', ucfirst($group->status)];
@endphp

<span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium ring-1 {{ $bg }} {{ $text }} {{ $ring }}">
    {{ $label }}
</span>
