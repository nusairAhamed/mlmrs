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
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">

        <div class="min-h-screen flex">

            {{-- Left panel — brand --}}
            <div class="hidden lg:flex lg:w-1/2 xl:w-2/5 flex-col justify-between bg-indigo-900 p-12">

                {{-- Logo --}}
                <div class="flex items-center gap-3">
                    <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-indigo-600 overflow-hidden shrink-0">
                        <svg viewBox="0 0 100 100" width="40" height="40" xmlns="http://www.w3.org/2000/svg">
                            <g stroke="rgba(255,255,255,0.18)" stroke-width="0.6">
                                <path d="M10 0 V100 M20 0 V100 M30 0 V100 M40 0 V100 M60 0 V100 M70 0 V100 M80 0 V100 M90 0 V100"/>
                                <path d="M0 10 H100 M0 20 H100 M0 30 H100 M0 40 H100 M0 60 H100 M0 70 H100 M0 80 H100 M0 90 H100"/>
                            </g>
                            <g stroke="rgba(255,255,255,0.85)" stroke-width="2" stroke-linecap="square">
                                <path d="M0 50 H100"/>
                                <path d="M50 0 V100"/>
                            </g>
                            <g fill="rgba(255,255,255,0.85)">
                                <polygon points="100,50 95,47 95,53"/>
                                <polygon points="50,0 47,5 53,5"/>
                            </g>
                            <path d="M50 70 C 30 56, 22 44, 30 36 C 36 30, 44 32, 50 40 C 56 32, 64 30, 70 36 C 78 44, 70 56, 50 70 Z" fill="#ffffff"/>
                            <polyline points="14,50 30,50 36,50 40,42 44,58 48,46 52,54 56,50 64,50 70,50 86,50"
                                fill="none" stroke="#4f46e5" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <span class="text-xl font-bold tracking-tight text-white">AxisMedLab</span>
                </div>

                {{-- Centre text --}}
                <div>
                    <h1 class="text-4xl font-bold text-white leading-snug">
                        Precision in<br>Every Result.
                    </h1>
                    <p class="mt-4 text-indigo-300 text-base leading-relaxed max-w-sm">
                        A complete medical laboratory management system — from patient registration to report delivery.
                    </p>

                    {{-- Feature list --}}
                    <ul class="mt-8 space-y-3">
                        @foreach([
                            'Lab order & sample tracking',
                            'QR-based patient & sample scanning',
                            'Automated report delivery',
                            'Role-based access control',
                        ] as $feature)
                        <li class="flex items-center gap-3 text-sm text-indigo-200">
                            <span class="flex items-center justify-center w-5 h-5 rounded-full bg-indigo-700 shrink-0">
                                <svg class="w-3 h-3 text-indigo-300" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                                </svg>
                            </span>
                            {{ $feature }}
                        </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Footer --}}
                <p class="text-xs text-indigo-500">© {{ date('Y') }} AxisMedLab. All rights reserved.</p>
            </div>

            {{-- Right panel — form --}}
            <div class="flex flex-1 flex-col justify-center items-center px-6 py-12 bg-gray-50">

                {{-- Mobile logo --}}
                <div class="flex lg:hidden items-center gap-3 mb-8">
                    <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-indigo-600 overflow-hidden shrink-0">
                        <svg viewBox="0 0 100 100" width="40" height="40" xmlns="http://www.w3.org/2000/svg">
                            <g stroke="rgba(255,255,255,0.85)" stroke-width="3" stroke-linecap="square">
                                <path d="M6 50 H94"/>
                                <path d="M50 6 V94"/>
                            </g>
                            <g fill="rgba(255,255,255,0.85)">
                                <polygon points="94,50 88,46 88,54"/>
                                <polygon points="50,6 46,12 54,12"/>
                            </g>
                            <path d="M50 72 C 28 56, 18 42, 28 32 C 36 24, 46 28, 50 38 C 54 28, 64 24, 72 32 C 82 42, 72 56, 50 72 Z" fill="#ffffff"/>
                            <polyline points="10,50 30,50 36,50 41,40 46,60 50,46 54,54 58,50 70,50 90,50"
                                fill="none" stroke="#4f46e5" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <span class="text-xl font-bold tracking-tight text-indigo-900">AxisMedLab</span>
                </div>

                <div class="w-full max-w-sm">
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-gray-900">Welcome back</h2>
                        <p class="mt-1 text-sm text-gray-500">Sign in to your account to continue</p>
                    </div>

                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm px-8 py-8">
                        {{ $slot }}
                    </div>
                </div>
            </div>

        </div>

    </body>
</html>
