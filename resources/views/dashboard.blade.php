<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">Dashboard</h2>
                <p class="text-sm text-gray-500 mt-0.5">
                    Welcome back, {{ Auth::user()->name }} &mdash; {{ now()->format('l, d F Y') }}
                </p>
            </div>
            {{-- Quick action for Technician / Admin --}}
            @if(in_array($role, ['Admin', 'Lab Technician']))
            <a href="{{ route('samples.scan') }}"
               class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-indigo-700 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z" />
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M6.75 6.75h.75v.75h-.75v-.75zM6.75 17.25h.75v.75h-.75v-.75zM16.5 6.75h.75v.75h-.75v-.75zM13.5 13.5h.75v.75h-.75v-.75zM13.5 19.5h.75v.75h-.75v-.75zM19.5 13.5h.75v.75h-.75v-.75zM19.5 19.5h.75v.75h-.75v-.75zM16.5 16.5h.75v.75h-.75v-.75z" />
                </svg>
                Scan Sample
            </a>
            @endif
        </div>
    </x-slot>

    @php
        $colorMap = [
            'indigo'  => ['bg' => 'bg-indigo-50',  'icon' => 'bg-indigo-100 text-indigo-600',  'text' => 'text-indigo-700'],
            'blue'    => ['bg' => 'bg-blue-50',    'icon' => 'bg-blue-100 text-blue-600',    'text' => 'text-blue-700'],
            'amber'   => ['bg' => 'bg-amber-50',   'icon' => 'bg-amber-100 text-amber-600',   'text' => 'text-amber-700'],
            'red'     => ['bg' => 'bg-red-50',     'icon' => 'bg-red-100 text-red-600',     'text' => 'text-red-700'],
            'emerald' => ['bg' => 'bg-emerald-50', 'icon' => 'bg-emerald-100 text-emerald-600', 'text' => 'text-emerald-700'],
            'violet'  => ['bg' => 'bg-violet-50',  'icon' => 'bg-violet-100 text-violet-600',  'text' => 'text-violet-700'],
            'rose'    => ['bg' => 'bg-rose-50',    'icon' => 'bg-rose-100 text-rose-600',    'text' => 'text-rose-700'],
            'gray'    => ['bg' => 'bg-gray-50',    'icon' => 'bg-gray-100 text-gray-500',    'text' => 'text-gray-600'],
        ];

        $statusColors = [
            'pending'          => 'bg-gray-100 text-gray-700',
            'sample_collected' => 'bg-blue-100 text-blue-700',
            'sample_rejected'  => 'bg-red-100 text-red-700',
            'in_progress'      => 'bg-amber-100 text-amber-700',
            'pending_review'   => 'bg-violet-100 text-violet-700',
            'approved'         => 'bg-emerald-100 text-emerald-700',
            'cancelled'        => 'bg-rose-100 text-rose-700',
        ];

        $barColors = [
            'gray'    => 'bg-gray-400',
            'blue'    => 'bg-blue-500',
            'red'     => 'bg-red-500',
            'amber'   => 'bg-amber-500',
            'violet'  => 'bg-violet-500',
            'emerald' => 'bg-emerald-500',
            'rose'    => 'bg-rose-500',
        ];
    @endphp

    <div class="space-y-6 p-6">

        {{-- ── Period Filter ── --}}
        @php
            $periods = [
                'today'      => 'Today',
                'yesterday'  => 'Yesterday',
                'last_week'  => 'Last 7 Days',
                'this_month' => 'This Month',
                'last_month' => 'Last Month',
                'last_year'  => 'Last Year',
            ];
        @endphp
        <div class="flex flex-wrap items-center gap-2">
            @foreach($periods as $key => $label)
            <a href="{{ route('dashboard', ['period' => $key]) }}"
               class="px-3 py-1.5 rounded-lg text-xs font-medium border transition
                      {{ $period === $key
                          ? 'bg-indigo-600 text-white border-indigo-600'
                          : 'bg-white text-gray-600 border-gray-200 hover:border-indigo-400 hover:text-indigo-600' }}">
                {{ $label }}
            </a>
            @endforeach

            {{-- Custom range toggle --}}
            <button type="button" id="customRangeToggle"
                    class="px-3 py-1.5 rounded-lg text-xs font-medium border transition
                           {{ $period === 'custom'
                               ? 'bg-indigo-600 text-white border-indigo-600'
                               : 'bg-white text-gray-600 border-gray-200 hover:border-indigo-400 hover:text-indigo-600' }}">
                Custom Range
            </button>
        </div>

        {{-- Custom date range form --}}
        <div id="customRangeForm"
             class="{{ $period === 'custom' ? '' : 'hidden' }} bg-white border border-gray-200 rounded-lg p-4 flex flex-wrap items-end gap-4 shadow-sm">
            <form method="GET" action="{{ route('dashboard') }}" class="flex flex-wrap items-end gap-4 w-full">
                <input type="hidden" name="period" value="custom">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">From</label>
                    <input type="date" name="date_from"
                           value="{{ request('date_from', now()->startOfMonth()->format('Y-m-d')) }}"
                           max="{{ now()->format('Y-m-d') }}"
                           class="rounded-lg border border-gray-200 text-sm px-3 py-1.5 text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">To</label>
                    <input type="date" name="date_to"
                           value="{{ request('date_to', now()->format('Y-m-d')) }}"
                           max="{{ now()->format('Y-m-d') }}"
                           class="rounded-lg border border-gray-200 text-sm px-3 py-1.5 text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <button type="submit"
                        class="px-4 py-1.5 rounded-lg bg-indigo-600 text-white text-xs font-medium hover:bg-indigo-700 transition">
                    Apply
                </button>
            </form>
        </div>

        <script>
            document.getElementById('customRangeToggle').addEventListener('click', function () {
                document.getElementById('customRangeForm').classList.toggle('hidden');
            });
        </script>

        {{-- ── Stat Cards ── --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-{{ count($stats) > 4 ? '6' : '4' }} gap-4">
            @foreach($stats as $stat)
            @php $c = $colorMap[$stat['color']]; @endphp
            <div class="rounded-lg border border-gray-100 bg-white shadow-sm p-5 flex items-center gap-4">
                <div class="flex items-center justify-center w-12 h-12 rounded-lg {{ $c['icon'] }} shrink-0">
                    @if($stat['icon'] === 'patients')
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                    </svg>
                    @elseif($stat['icon'] === 'orders')
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                    </svg>
                    @elseif($stat['icon'] === 'approval')
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    @elseif($stat['icon'] === 'notification')
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                    </svg>
                    @elseif($stat['icon'] === 'users')
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                    @elseif($stat['icon'] === 'revenue')
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    @elseif($stat['icon'] === 'pending')
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                    @elseif($stat['icon'] === 'verify')
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    @elseif($stat['icon'] === 'done')
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                    </svg>
                    @endif
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-medium text-gray-500 truncate">{{ $stat['label'] }}</p>
                    <p class="text-2xl font-bold {{ $c['text'] }} leading-tight">{{ $stat['value'] }}</p>
                </div>
            </div>
            @endforeach
        </div>

        {{-- ── Ready for Collection (Admin + Receptionist only) ── --}}
        @if(in_array($role, ['Admin', 'Receptionist']) && $readyForCollection->isNotEmpty())
        <div class="bg-white rounded-lg border border-gray-100 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <span class="flex items-center justify-center w-7 h-7 rounded-full bg-amber-100">
                        <svg class="w-4 h-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                        </svg>
                    </span>
                    <h3 class="text-sm font-semibold text-gray-800">Ready for Collection</h3>
                    <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-700">
                        {{ $readyForCollection->count() }}
                    </span>
                </div>
                <a href="{{ route('lab-orders.index', ['status' => 'approved']) }}"
                   class="text-xs text-indigo-600 hover:underline font-medium">View all</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wide pb-2 pr-4">Order #</th>
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wide pb-2 pr-4">Patient</th>
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wide pb-2 pr-4">Status</th>
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wide pb-2 pr-4">Since</th>
                            <th class="pb-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($readyForCollection as $order)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-2.5 pr-4 font-mono text-xs text-gray-700">{{ $order->order_number }}</td>
                            <td class="py-2.5 pr-4 font-medium text-gray-800">{{ $order->patient?->full_name ?? '—' }}</td>
                            <td class="py-2.5 pr-4">
                                @if($order->status === 'on_hold')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-700">
                                        On Hold
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                                        Approved
                                    </span>
                                @endif
                            </td>
                            <td class="py-2.5 pr-4 text-xs text-gray-500">{{ $order->updated_at->format('d M Y, h:i A') }}</td>
                            <td class="py-2.5 text-right">
                                <a href="{{ route('lab-orders.show', $order) }}"
                                   class="text-xs text-indigo-600 hover:underline font-medium">View</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- ── Two-column layout: status breakdown + recent orders ── --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Order Status Breakdown --}}
            <div class="bg-white rounded-lg border border-gray-100 shadow-sm p-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-4">Order Status Breakdown</h3>
                <div class="space-y-3">
                    @foreach($orderStats as $s)
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs text-gray-600">{{ $s['label'] }}</span>
                            <span class="text-xs font-semibold text-gray-800">{{ $s['count'] }}
                                <span class="text-gray-400 font-normal">({{ $s['percent'] }}%)</span>
                            </span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2">
                            <div class="{{ $barColors[$s['color']] }} h-2 rounded-full transition-all"
                                 style="width: {{ $s['percent'] }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Recent Orders --}}
            <div class="lg:col-span-2 bg-white rounded-lg border border-gray-100 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-gray-700">
                        {{ $role === 'Lab Technician' ? 'Orders Needing Attention' : 'Recent Orders' }}
                    </h3>
                    <a href="{{ route('lab-orders.index') }}"
                       class="text-xs text-indigo-600 hover:underline font-medium">View all</a>
                </div>

                @if($recentOrders->isEmpty())
                <div class="flex flex-col items-center justify-center py-10 text-gray-400">
                    <svg class="w-10 h-10 mb-2 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                    </svg>
                    <p class="text-sm">No orders found</p>
                </div>
                @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-100">
                                <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wide pb-2 pr-4">Order #</th>
                                <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wide pb-2 pr-4">Patient</th>
                                <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wide pb-2 pr-4">Status</th>
                                <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wide pb-2 pr-4">Date</th>
                                <th class="pb-2"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($recentOrders as $order)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="py-2.5 pr-4 font-mono text-xs text-gray-700">{{ $order->order_number }}</td>
                                <td class="py-2.5 pr-4 text-gray-800 font-medium">{{ $order->patient?->full_name ?? '—' }}</td>
                                <td class="py-2.5 pr-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-600' }}">
                                        {{ ucwords(str_replace('_', ' ', $order->status)) }}
                                    </span>
                                </td>
                                <td class="py-2.5 pr-4 text-xs text-gray-500">{{ $order->created_at->format('d M Y') }}</td>
                                <td class="py-2.5 text-right">
                                    <a href="{{ route('lab-orders.show', $order) }}"
                                       class="text-xs text-indigo-600 hover:underline font-medium">View</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>

        </div>

    </div>
</x-app-layout>
