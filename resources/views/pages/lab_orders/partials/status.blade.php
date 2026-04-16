<?php
    $status = $order->status ?? 'pending';

    $map = [
        'pending'          => ['bg' => 'bg-yellow-50',  'text' => 'text-yellow-700',  'ring' => 'ring-yellow-200',  'label' => 'Pending'],
        'sample_collected' => ['bg' => 'bg-blue-50',    'text' => 'text-blue-700',    'ring' => 'ring-blue-200',    'label' => 'Sample Collected'],
        'in_progress'      => ['bg' => 'bg-indigo-50',  'text' => 'text-indigo-700',  'ring' => 'ring-indigo-200',  'label' => 'In Progress'],
        'pending_review'   => ['bg' => 'bg-orange-50',  'text' => 'text-orange-700',  'ring' => 'ring-orange-200',  'label' => 'Pending Review'],
        'approved'         => ['bg' => 'bg-green-50',   'text' => 'text-green-700',   'ring' => 'ring-green-200',   'label' => 'Approved'],
        'sample_rejected'  => ['bg' => 'bg-red-50',     'text' => 'text-red-700',     'ring' => 'ring-red-200',     'label' => 'Sample Rejected'],
        'on_hold'          => ['bg' => 'bg-purple-50',  'text' => 'text-purple-700',  'ring' => 'ring-purple-200',  'label' => 'On Hold'],
        'cancelled'        => ['bg' => 'bg-gray-100',   'text' => 'text-gray-500',    'ring' => 'ring-gray-200',    'label' => 'Cancelled'],
    ];

    $c = $map[$status] ?? $map['pending'];
?>

<span class="inline-flex items-center rounded-full {{ $c['bg'] }} px-2 py-1 text-xs font-medium {{ $c['text'] }} ring-1 {{ $c['ring'] }}">
    {{ $c['label'] }}
</span>
