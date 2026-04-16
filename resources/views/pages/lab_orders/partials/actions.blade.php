<div class="relative inline-block text-left" x-data="{ open: false }" @click.outside="open = false">

    <button @click="open = !open"
            class="inline-flex items-center gap-1 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-50">
        Actions
        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <div x-show="open" x-transition
         class="absolute right-0 z-50 mt-1 w-48 rounded-lg border border-gray-100 bg-white shadow-lg"
         style="display: none;">

        <div class="py-1">

            {{-- Always available --}}
            <a href="{{ route('lab-orders.show', $order) }}"
               class="flex items-center gap-2 px-4 py-2 text-xs text-gray-700 hover:bg-gray-50">
                <svg class="h-3.5 w-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                View Order
            </a>

            {{-- Samples: pending, sample_collected, sample_rejected --}}
            @if(in_array($order->status, ['pending', 'sample_collected', 'sample_rejected']))
                <a href="{{ route('lab-orders.samples.index', $order) }}"
                   class="flex items-center gap-2 px-4 py-2 text-xs text-indigo-700 hover:bg-indigo-50">
                    <svg class="h-3.5 w-3.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Samples
                </a>
            @endif

            {{-- Results: sample_collected, in_progress, pending_review, approved --}}
            @if(in_array($order->status, ['sample_collected', 'in_progress', 'pending_review', 'approved']))
                <a href="{{ route('lab-orders.results.index', $order) }}"
                   class="flex items-center gap-2 px-4 py-2 text-xs text-emerald-700 hover:bg-emerald-50">
                    <svg class="h-3.5 w-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Results
                </a>
            @endif

            {{-- Report & PDF: approved only --}}
            @if($order->status === 'approved')
                <a href="{{ route('lab-reports.show', $order) }}"
                   class="flex items-center gap-2 px-4 py-2 text-xs text-blue-700 hover:bg-blue-50">
                    <svg class="h-3.5 w-3.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    View Report
                </a>

                <a href="{{ route('lab-reports.pdf', $order) }}"
                   class="flex items-center gap-2 px-4 py-2 text-xs text-gray-700 hover:bg-gray-50">
                    <svg class="h-3.5 w-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Download PDF
                </a>
            @endif

            {{-- Edit: pending only --}}
            @if($order->status === 'pending')
                <div class="my-1 border-t border-gray-100"></div>

                <a href="{{ route('lab-orders.edit', $order) }}"
                   class="flex items-center gap-2 px-4 py-2 text-xs text-gray-700 hover:bg-gray-50">
                    <svg class="h-3.5 w-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
                </a>
            @endif

            {{-- Cancel: pending only --}}
            @if($order->status === 'pending')
                <div class="my-1 border-t border-gray-100"></div>

                <form action="{{ route('lab-orders.cancel', $order) }}" method="POST"
                      onsubmit="return confirm('Cancel this order? This cannot be undone.');">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                            class="flex w-full items-center gap-2 px-4 py-2 text-xs text-orange-600 hover:bg-orange-50">
                        <svg class="h-3.5 w-3.5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                        </svg>
                        Cancel Order
                    </button>
                </form>
            @endif

        </div>
    </div>
</div>
