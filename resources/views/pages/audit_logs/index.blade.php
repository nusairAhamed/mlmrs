<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Audit Log</h2>
    </x-slot>

    @php
        $fieldLabels = \App\Http\Controllers\Admin\AuditLogController::$fieldLabels;
        $statusLabels = \App\Http\Controllers\Admin\AuditLogController::$statusLabels;

        // Pre-load patient and user maps for resolving IDs to names
        $patientMap = \App\Models\Patient::pluck('full_name', 'id');
        $userMap    = \App\Models\User::pluck('name', 'id');

        $eventBadge = fn($e) => match($e) {
            'created'  => ['bg-green-50 text-green-700 ring-green-200',  'Created'],
            'updated'  => ['bg-yellow-50 text-yellow-700 ring-yellow-200', 'Updated'],
            'deleted'  => ['bg-red-50 text-red-700 ring-red-200',         'Deleted'],
            'restored' => ['bg-blue-50 text-blue-700 ring-blue-200',      'Restored'],
            default    => ['bg-gray-50 text-gray-700 ring-gray-200',       ucfirst($e)],
        };

        $modelLabel = fn($type) => match($type) {
            'App\Models\LabOrder'     => 'Lab Order',
            'App\Models\LabOrderTest' => 'Test Result',
            'App\Models\Patient'      => 'Patient',
            'App\Models\User'         => 'User',
            default => class_basename($type),
        };

        $formatValue = function($field, $value) use ($statusLabels, $patientMap, $userMap) {
            if (is_null($value))  return '<span class="italic text-gray-400">—</span>';
            if (is_bool($value))  return $value
                ? '<span class="text-red-600 font-medium">Yes (Abnormal)</span>'
                : '<span class="text-green-700 font-medium">No (Normal)</span>';

            if ($field === 'status' && isset($statusLabels[$value]))
                return '<span class="font-medium">' . e($statusLabels[$value]) . '</span>';

            if ($field === 'is_abnormal')
                return $value
                    ? '<span class="text-red-600 font-medium">Abnormal</span>'
                    : '<span class="text-green-700 font-medium">Normal</span>';

            if ($field === 'patient_id')
                return '<span>' . e($patientMap[$value] ?? 'Patient #' . $value) . '</span>';

            if (in_array($field, ['approved_by', 'entered_by', 'verified_by']))
                return '<span>' . e($userMap[$value] ?? 'User #' . $value) . '</span>';

            if ($field === 'total_amount' && is_numeric($value))
                return '<span>Rs ' . e(number_format((float)$value, 2)) . '</span>';

            return '<span>' . e($value) . '</span>';
        };

        $recordLink = function($audit) use ($modelLabel) {
            $label = $modelLabel($audit->auditable_type) . ' #' . $audit->auditable_id;
            $url   = match($audit->auditable_type) {
                'App\Models\LabOrder'     => route('lab-orders.show', $audit->auditable_id),
                'App\Models\LabOrderTest' => route('lab-orders.show', $audit->auditable?->lab_order_id ?? $audit->auditable_id),
                'App\Models\Patient'      => route('patients.show', $audit->auditable_id),
                'App\Models\User'         => route('users.edit', $audit->auditable_id),
                default => null,
            };
            return $url
                ? "<a href=\"{$url}\" class=\"font-semibold text-indigo-600 hover:text-indigo-800 hover:underline\">{$label}</a>"
                : "<span class=\"font-semibold text-indigo-700\">{$label}</span>";
        };

        $recordDetail = function($audit) {
            $r = $audit->auditable;
            if (!$r) return null;

            if ($audit->auditable_type === 'App\Models\LabOrder') {
                $parts = [];
                if ($r->order_number)   $parts[] = $r->order_number;
                if ($r->patient)        $parts[] = $r->patient->full_name;
                if ($r->total_amount)   $parts[] = 'Rs ' . number_format((float) $r->total_amount, 2);
                $groupCount = $r->groups()->count();
                if ($groupCount)        $parts[] = $groupCount . ' panel' . ($groupCount !== 1 ? 's' : '');
                return implode(' · ', $parts);
            }

            if ($audit->auditable_type === 'App\Models\LabOrderTest') {
                $parts = [];
                if ($r->test_name) $parts[] = $r->test_name;
                if ($r->order)     $parts[] = 'Order ' . $r->order->order_number;
                return implode(' · ', $parts);
            }

            return match($audit->auditable_type) {
                'App\Models\Patient' => trim(($r->patient_code ?? '') . ($r->full_name ? ' · ' . $r->full_name : '')),
                'App\Models\User'    => $r->email ?? '',
                default => null,
            };
        };

        $summaryLine = function($audit) use ($modelLabel, $statusLabels) {
            $model  = $modelLabel($audit->auditable_type);
            $new    = $audit->new_values ?? [];
            $old    = $audit->old_values ?? [];

            if ($audit->event === 'created') {
                $status = isset($new['status']) ? ($statusLabels[$new['status']] ?? $new['status']) : null;
                return $status ? "{$model} created with status <strong>{$status}</strong>" : "{$model} record created";
            }

            if ($audit->event === 'deleted') return "{$model} record deleted";

            // Updated — build a natural sentence
            if (isset($new['status'])) {
                $from = isset($old['status']) ? ($statusLabels[$old['status']] ?? $old['status']) : null;
                $to   = $statusLabels[$new['status']] ?? $new['status'];
                return $from
                    ? "Status changed from <strong>{$from}</strong> to <strong>{$to}</strong>"
                    : "Status set to <strong>{$to}</strong>";
            }

            if (isset($new['result_value'])) {
                $from = $old['result_value'] ?? null;
                $to   = $new['result_value'];
                return $from
                    ? "Result changed from <strong>" . e($from) . "</strong> to <strong>" . e($to) . "</strong>"
                    : "Result entered: <strong>" . e($to) . "</strong>";
            }

            if (isset($new['full_name'])) {
                return "Name updated from <strong>" . e($old['full_name'] ?? '—') . "</strong> to <strong>" . e($new['full_name']) . "</strong>";
            }

            $count = count($new);
            return "{$count} field" . ($count !== 1 ? 's' : '') . " updated on {$model}";
        };
    @endphp

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Filters --}}
            <form method="GET" action="{{ route('audit-logs.index') }}"
                  class="bg-white rounded-lg shadow-sm ring-1 ring-gray-200 p-4 mb-6">
                <div class="flex flex-wrap gap-3 items-end">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Model</label>
                        <select name="model"
                                class="rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
                            <option value="">All Models</option>
                            @foreach(array_keys($modelMap) as $label)
                                <option value="{{ $label }}" {{ request('model') === $label ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Event</label>
                        <select name="event"
                                class="rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
                            <option value="">All Events</option>
                            @foreach(['created', 'updated', 'deleted', 'restored'] as $ev)
                                <option value="{{ $ev }}" {{ request('event') === $ev ? 'selected' : '' }}>
                                    {{ ucfirst($ev) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs text-gray-500 mb-1">User</label>
                        <select name="user_id"
                                class="rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
                            <option value="">All Users</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                                    {{ $u->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs text-gray-500 mb-1">From</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}"
                               class="rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
                    </div>

                    <div>
                        <label class="block text-xs text-gray-500 mb-1">To</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}"
                               class="rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
                    </div>

                    <button type="submit"
                            class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                        Filter
                    </button>

                    @if(request()->hasAny(['model', 'event', 'user_id', 'date_from', 'date_to']))
                        <a href="{{ route('audit-logs.index') }}"
                           class="rounded-lg px-4 py-2 text-sm font-medium text-gray-600 ring-1 ring-gray-200 hover:bg-gray-50">
                            Clear
                        </a>
                    @endif

                    <div class="ml-auto text-xs text-gray-400 self-center">
                        {{ number_format($audits->total()) }} record{{ $audits->total() !== 1 ? 's' : '' }}
                    </div>
                </div>
            </form>

            {{-- Log entries --}}
            <div class="space-y-3">
                @forelse($rows as $row)
                    @php $audit = $row['items'][0]; @endphp
                    @php [$badgeClass, $badgeLabel] = $eventBadge($audit->event); @endphp

                    <div class="bg-white rounded-lg shadow-sm ring-1 ring-gray-200 p-4">
                        <div class="flex items-start gap-4">

                            {{-- Left: timestamp + user --}}
                            <div class="w-36 shrink-0">
                                <div class="text-xs font-medium text-gray-800">{{ $audit->created_at->format('d M Y') }}</div>
                                <div class="text-xs text-gray-400">{{ $audit->created_at->format('h:i A') }}</div>
                                <div class="mt-2 text-xs font-semibold text-gray-900">{{ $audit->user?->name ?? 'System' }}</div>
                                <div class="text-xs text-gray-400">{{ $audit->user?->role->name ?? '' }}</div>
                            </div>

                            {{-- Middle --}}
                            <div class="flex-1 min-w-0">

                                @if($row['type'] === 'test_group')
                                    {{-- Grouped test results --}}
                                    @php
                                        $firstAudit = $row['items'][0];
                                        $orderNumber = $firstAudit->auditable?->order?->order_number ?? ('Order #' . $row['order_id']);
                                        $eventNew = $firstAudit->new_values['status'] ?? null;
                                        $eventOld = $firstAudit->old_values['status'] ?? null;
                                        $statusTo = $eventNew ? ($statusLabels[$eventNew] ?? $eventNew) : null;
                                        $statusFrom = $eventOld ? ($statusLabels[$eventOld] ?? $eventOld) : null;
                                    @endphp
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 {{ $badgeClass }}">{{ $badgeLabel }}</span>
                                        <span class="text-xs font-semibold text-indigo-600">{{ count($row['items']) }} Test Results</span>
                                        <span class="text-xs text-gray-400">· {{ $orderNumber }}</span>
                                    </div>
                                    <p class="text-sm text-gray-700 mb-3">
                                        @if($statusFrom && $statusTo)
                                            Results status changed from <strong>{{ $statusFrom }}</strong> to <strong>{{ $statusTo }}</strong>
                                        @else
                                            {{ count($row['items']) }} test results updated
                                        @endif
                                    </p>
                                    <div class="rounded-lg bg-gray-50 ring-1 ring-gray-100 divide-y divide-gray-100">
                                        @foreach($row['items'] as $t)
                                            @php $tn = $t->new_values ?? []; $to = $t->old_values ?? []; @endphp
                                            <div class="px-3 py-2">
                                                <div class="text-xs font-semibold text-gray-700 mb-1">
                                                    {{ $t->auditable?->test_name ?? 'Test #' . $t->auditable_id }}
                                                </div>
                                                <div class="flex flex-wrap gap-x-6 gap-y-1 text-xs text-gray-600">
                                                    @if(array_key_exists('result_value', $tn))
                                                        <span>
                                                            Result:
                                                            @if(array_key_exists('result_value', $to))
                                                                <span class="line-through text-red-400">{!! $formatValue('result_value', $to['result_value']) !!}</span> →
                                                            @endif
                                                            <span class="font-medium text-gray-900">{!! $formatValue('result_value', $tn['result_value']) !!}</span>
                                                        </span>
                                                    @endif
                                                    @if(array_key_exists('is_abnormal', $tn))
                                                        <span>{!! $formatValue('is_abnormal', $tn['is_abnormal']) !!}</span>
                                                    @endif
                                                    @if(array_key_exists('status', $tn))
                                                        <span>Status: <span class="font-medium">{!! $formatValue('status', $tn['status']) !!}</span></span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                @else
                                    {{-- Single audit entry --}}
                                    @php $new = $audit->new_values ?? []; $old = $audit->old_values ?? []; @endphp
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 {{ $badgeClass }}">{{ $badgeLabel }}</span>
                                        <span class="text-xs">{!! $recordLink($audit) !!}</span>
                                    </div>
                                    @php $detail = $recordDetail($audit); @endphp
                                    @if($detail)
                                        <div class="text-xs text-gray-400 mb-2">{{ $detail }}</div>
                                    @endif
                                    <p class="text-sm text-gray-700 mb-3">{!! $summaryLine($audit) !!}</p>

                                    @if(!empty($new))
                                        <div class="rounded-lg bg-gray-50 ring-1 ring-gray-100 divide-y divide-gray-100">
                                            @foreach($new as $field => $newVal)
                                                <div class="flex items-start gap-3 px-3 py-2 text-xs">
                                                    <span class="w-32 shrink-0 font-medium text-gray-500">
                                                        {{ $fieldLabels[$field] ?? ucwords(str_replace('_', ' ', $field)) }}
                                                    </span>
                                                    @if(array_key_exists($field, $old))
                                                        <span class="line-through text-red-400">{!! $formatValue($field, $old[$field]) !!}</span>
                                                        <span class="text-gray-300">→</span>
                                                    @endif
                                                    <span class="text-gray-900">
                                                        @if($field === 'samples' && is_array($newVal))
                                                            <div class="space-y-1.5">
                                                                @foreach($newVal as $sample)
                                                                    <div class="flex items-center gap-2 flex-wrap">
                                                                        <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-0.5 font-mono text-gray-700 ring-1 ring-gray-200">{{ $sample['code'] ?? '?' }}</span>
                                                                        <span class="text-gray-600">{{ ucwords(str_replace('_', ' ', $sample['type'] ?? '')) }}</span>
                                                                        @if(!empty($sample['collected_at']))<span class="text-gray-400">· {{ $sample['collected_at'] }}</span>@endif
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @elseif($field === 'panels' && is_array($newVal))
                                                            <div class="space-y-2">
                                                                @foreach($newVal as $panel)
                                                                    @php $panelName = is_array($panel) ? ($panel['name'] ?? '?') : $panel; $panelTests = is_array($panel) ? ($panel['tests'] ?? []) : []; @endphp
                                                                    <div>
                                                                        <span class="inline-flex rounded-full bg-indigo-50 px-2.5 py-0.5 font-medium text-indigo-700 ring-1 ring-indigo-200">{{ $panelName }}</span>
                                                                        @if(!empty($panelTests))
                                                                            <div class="mt-1.5 ml-2 flex flex-wrap gap-1">
                                                                                @foreach($panelTests as $test)
                                                                                    <span class="inline-flex rounded-full bg-gray-100 px-2 py-0.5 text-gray-600 ring-1 ring-gray-200">{{ $test }}</span>
                                                                                @endforeach
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            {!! $formatValue($field, $newVal) !!}
                                                        @endif
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                @endif
                            </div>

                            {{-- Right: IP --}}
                            <div class="shrink-0 text-right">
                                <div class="text-xs text-gray-400">{{ $audit->ip_address }}</div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-lg shadow-sm ring-1 ring-gray-200 p-10 text-center text-gray-400 text-sm">
                        No audit records found.
                    </div>
                @endforelse
            </div>

            @if($audits->hasPages())
                <div class="mt-4">
                    {{ $audits->links() }}
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
