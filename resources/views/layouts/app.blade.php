<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'AxisMedLab') }}</title>

        <link rel="icon" type="image/svg+xml" href="/favicon.svg">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

        <!-- DataTables 2.3.7 -->
        <script src="https://cdn.datatables.net/2.3.7/js/dataTables.min.js"></script>
        <link rel="stylesheet" href="https://cdn.datatables.net/2.3.7/css/dataTables.tailwindcss.min.css">
        <script src="https://cdn.datatables.net/2.3.7/js/dataTables.tailwindcss.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                if (!window.jQuery?.fn?.dataTable) return;
                const dt = window.jQuery.fn.dataTable;

                dt.defaults.renderer = 'tailwindcss';
                dt.defaults.layout = {
                    topStart: 'pageLength',
                    topEnd: 'search',
                    bottomStart: 'info',
                    bottomEnd: 'paging'
                };

                dt.ext.classes.search.input =
                    'ml-2 w-64 max-w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 ' +
                    'placeholder-gray-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20';


                dt.ext.classes.length.select =
                    'ml-2 rounded-lg border border-gray-200 bg-white px-3 py-2 pr-8 text-sm text-gray-700 ' +
                    'focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20';

                dt.ext.classes.table       = 'dataTable w-full text-sm text-gray-800 align-middle';
                dt.ext.classes.thead.row   = 'border-b border-gray-200';
                dt.ext.classes.thead.cell  = 'px-4 py-3 text-xs font-semibold uppercase tracking-wide text-gray-600 bg-gray-50 text-left';
                dt.ext.classes.tbody.row   = 'hover:bg-gray-50';
                dt.ext.classes.tbody.cell  = 'px-4 py-3 border-b border-gray-100';

                dt.ext.classes.paging.button =
                    'relative inline-flex items-center justify-center px-3 py-2 -mr-px text-sm font-medium ' +
                    'border border-gray-200 bg-white hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-2 focus:ring-indigo-500/20';

                dt.ext.classes.paging.active    = 'bg-indigo-600 text-white border-indigo-600';
                dt.ext.classes.paging.notActive = 'bg-white text-gray-700';
                dt.ext.classes.paging.enabled   = 'text-gray-700 hover:text-gray-900';
                dt.ext.classes.paging.notEnabled = 'text-gray-300 cursor-not-allowed opacity-60';
                dt.ext.classes.paging.first     = 'rounded-l-lg';
                dt.ext.classes.paging.last      = 'rounded-r-lg';
                dt.ext.classes.container        = 'dt-container dt-tailwindcss';

                // Set placeholder on all DT search inputs after each table init
                $(document).on('init.dt', function() {
                    $('.dt-search input').attr('placeholder', 'Search…');
                });
            });
        </script>

        <!-- App Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            /* Active pagination page */
            .dt-paging a[aria-current="page"],
            .dt-paging a[aria-current="page"]:hover {
                background-color: #4f46e5 !important;
                color: #ffffff !important;
                border-color: #4f46e5 !important;
            }

            /* entries per page */
            .dt-length label { font-size: 0.875rem; color: #374151; white-space: nowrap; }
            .dt-length label br { display: none !important; }

            /* Bottom row: info left, pagination right */
            .dt-paging { display: flex !important; justify-content: flex-end !important; }
            .dt-info { font-size: 0.875rem !important; color: #6b7280 !important; }

            /* Search — hide label text, input aligned right */
            .dt-search {
                display: flex !important;
                align-items: center !important;
                justify-content: flex-end !important;
            }
            .dt-search label { font-size: 0; }
            .dt-search input { margin-left: 0 !important; font-size: 0.875rem !important; }
        </style>
    </head>

    <body class="font-sans antialiased bg-gray-100">

        {{-- Alpine root: controls sidebar open/close state --}}
        <div x-data="{ sidebarOpen: false }" class="flex h-screen overflow-hidden">

            {{-- ── Sidebar ── --}}
            @include('layouts.navigation')

            {{-- ── Main content area ── --}}
            <div class="flex flex-col flex-1 min-w-0 overflow-auto">

                {{-- Top header bar --}}
                <header class="shrink-0 flex items-center justify-between h-16 px-4 sm:px-6 bg-white border-b border-gray-200 shadow-sm z-10">

                    {{-- Mobile: hamburger --}}
                    <button @click="sidebarOpen = !sidebarOpen"
                            class="sm:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100 focus:outline-none">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>

                    {{-- Page title (from $header slot) --}}
                    <div class="flex-1 min-w-0 px-2 sm:px-0">
                        @isset($header)
                            {{ $header }}
                        @endisset
                    </div>

                    {{-- Bell notification icon --}}
                    <div class="relative ml-4" x-data="bellNotifications()" x-init="init()">
                        <button @click="toggle()" class="relative p-2 rounded-lg text-gray-500 hover:bg-gray-100 focus:outline-none">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                            </svg>
                            <span x-show="count > 0"
                                  x-text="count > 9 ? '9+' : count"
                                  class="absolute -top-0.5 -right-0.5 flex items-center justify-center w-4 h-4 rounded-full bg-red-500 text-white text-[10px] font-bold leading-none">
                            </span>
                        </button>

                        {{-- Dropdown --}}
                        <div x-show="open"
                             @click.outside="open = false"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             style="display:none"
                             class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg ring-1 ring-gray-200 z-50 overflow-hidden">

                            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
                                <span class="text-sm font-semibold text-gray-800">Notifications</span>
                                <button @click="markAllRead()" x-show="count > 0"
                                        class="text-xs text-indigo-600 hover:underline font-medium">
                                    Mark all read
                                </button>
                            </div>

                            <div class="max-h-80 overflow-y-auto divide-y divide-gray-50">
                                <template x-if="notifications.length === 0">
                                    <div class="px-4 py-6 text-center text-sm text-gray-400">
                                        No new notifications
                                    </div>
                                </template>
                                <template x-for="n in notifications" :key="n.id">
                                    <a :href="n.order_url"
                                       @click="markRead(n.id)"
                                       class="block px-4 py-3 hover:bg-gray-50 transition">
                                        <div class="flex items-start gap-3">
                                            <div :class="n.type === 'report_on_hold'
                                                    ? 'bg-purple-100 text-purple-600'
                                                    : 'bg-green-100 text-green-600'"
                                                 class="mt-0.5 flex items-center justify-center w-7 h-7 rounded-full shrink-0">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                                                </svg>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-xs font-semibold text-gray-800 leading-snug" x-text="n.title"></p>
                                                <p class="text-xs text-gray-500 mt-0.5 leading-snug" x-text="n.message"></p>
                                                <p class="text-[10px] text-gray-400 mt-1" x-text="n.created_at"></p>
                                            </div>
                                        </div>
                                    </a>
                                </template>
                            </div>
                        </div>
                    </div>

                </header>

                {{-- Page content --}}
                <main class="flex-1 overflow-auto p-4 sm:p-6 lg:p-8">
                    {{ $slot }}
                </main>

            </div>
        </div>

        <script>
        function bellNotifications() {
            return {
                open: false,
                count: 0,
                notifications: [],
                csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),

                init() {
                    this.fetch();
                    setInterval(() => this.fetch(), 10000);
                    document.addEventListener('visibilitychange', () => {
                        if (document.visibilityState === 'visible') this.fetch();
                    });
                },

                fetch() {
                    fetch('{{ route('staff-notifications.unread') }}')
                        .then(r => r.json())
                        .then(data => {
                            this.count = data.count;
                            this.notifications = data.notifications;
                        })
                        .catch(() => {});
                },

                toggle() {
                    this.open = !this.open;
                },

                markRead(id) {
                    fetch(`/staff-notifications/${id}/read`, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': this.csrfToken, 'Content-Type': 'application/json' },
                    }).then(() => this.fetch());
                },

                markAllRead() {
                    fetch('{{ route('staff-notifications.read-all') }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': this.csrfToken, 'Content-Type': 'application/json' },
                    }).then(() => {
                        this.count = 0;
                        this.notifications = [];
                        this.open = false;
                    });
                },
            };
        }
        </script>

        @stack('scripts')
    </body>
</html>
