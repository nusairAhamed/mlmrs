<?php
    $status = $order->status ?? 'pending';

    $map = [
        'pending' => ['bg' => 'bg-yellow-50', 'text' => 'text-yellow-700', 'ring' => 'ring-yellow-200', 'label' => 'Pending'],
        'in_progress' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'ring' => 'ring-blue-200', 'label' => 'In Progress'],
        'completed' => ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-700', 'ring' => 'ring-indigo-200', 'label' => 'Completed'],
        'approved' => ['bg' => 'bg-green-50', 'text' => 'text-green-700', 'ring' => 'ring-green-200', 'label' => 'Approved'],
    ];

    $c = $map[$status] ?? $map['pending'];
?>

<span class="inline-flex items-center rounded-full {{ $c['bg'] }} px-2 py-1 text-xs font-medium {{ $c['text'] }} ring-1 {{ $c['ring'] }}">
    {{ $c['label'] }}
</span>