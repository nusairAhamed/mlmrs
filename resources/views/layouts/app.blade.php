<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

           <!-- jQuery (only if you need it elsewhere) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- DataTables core 2.3.7 -->
<script src="https://cdn.datatables.net/2.3.7/js/dataTables.min.js"></script>

<!-- Tailwind CSS integration 2.3.7 -->
<link rel="stylesheet"
      href="https://cdn.datatables.net/2.3.7/css/dataTables.tailwindcss.min.css">

<script src="https://cdn.datatables.net/2.3.7/js/dataTables.tailwindcss.js"></script>

<script>
     document.addEventListener('DOMContentLoaded', () => {
  // Works for CDN + jQuery integration file
  if (!window.jQuery?.fn?.dataTable) return;

  const dt = window.jQuery.fn.dataTable;

  // Ensure Tailwind renderer
  dt.defaults.renderer = 'tailwindcss';

  // Optional: better control layout (top: length + search, bottom: info + paging)
  dt.defaults.layout = {
    topStart: 'pageLength',
    topEnd: 'search',
    bottomStart: 'info',
    bottomEnd: 'paging'
  };

  // ====== THEME OVERRIDES ======

  // Inputs
  dt.ext.classes.search.input =
    'ml-2 w-64 max-w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 ' +
    'placeholder-gray-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20';

  dt.ext.classes.length.select =
    'ml-2 rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 ' +
    'focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20';

  // Table
  dt.ext.classes.table =
    'dataTable w-full text-sm text-gray-800 align-middle';

  dt.ext.classes.thead.row =
    'border-b border-gray-200';

  dt.ext.classes.thead.cell =
    'px-4 py-3 text-xs font-semibold uppercase tracking-wide text-gray-600 bg-gray-50 text-left';

  dt.ext.classes.tbody.row =
    'hover:bg-gray-50';

  dt.ext.classes.tbody.cell =
    'px-4 py-3 border-b border-gray-100';

  // Pagination (nice, modern)
  dt.ext.classes.paging.button =
    'relative inline-flex items-center justify-center px-3 py-2 -mr-px text-sm font-medium ' +
    'border border-gray-200 bg-white hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-2 focus:ring-indigo-500/20';

  dt.ext.classes.paging.active =
    'bg-indigo-600 text-white border-indigo-600';

  dt.ext.classes.paging.notActive =
    'bg-white text-gray-700';

  dt.ext.classes.paging.enabled =
    'text-gray-700 hover:text-gray-900';

  dt.ext.classes.paging.notEnabled =
    'text-gray-300 cursor-not-allowed opacity-60';

  dt.ext.classes.paging.first = 'rounded-l-xl';
  dt.ext.classes.paging.last = 'rounded-r-xl';

  // Container spacing
  dt.ext.classes.container = 'dt-container dt-tailwindcss';
});
</script>
        


        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">

    
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
        @stack('scripts')
    </body>
</html>
