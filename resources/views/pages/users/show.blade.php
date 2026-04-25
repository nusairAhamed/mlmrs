<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">User Profile</h2>
        <p class="text-sm text-gray-500 mt-0.5">{{ $user->name }}</p>
    </x-slot>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

        {{-- Avatar + name card --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 flex items-center gap-5">
            <div class="flex items-center justify-center w-16 h-16 rounded-full bg-indigo-600 text-white text-2xl font-bold shrink-0">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900">{{ $user->name }}</h3>
                <p class="text-sm text-gray-500">{{ $user->email }}</p>
                <span class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700">
                    {{ $user->role->name ?? '—' }}
                </span>
            </div>
        </div>

        {{-- Details --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm divide-y divide-gray-50">

            <div class="flex items-center justify-between px-6 py-4">
                <span class="text-sm font-medium text-gray-500">Full Name</span>
                <span class="text-sm text-gray-800 font-medium">{{ $user->name }}</span>
            </div>

            <div class="flex items-center justify-between px-6 py-4">
                <span class="text-sm font-medium text-gray-500">Email Address</span>
                <span class="text-sm text-gray-800">{{ $user->email }}</span>
            </div>

            <div class="flex items-center justify-between px-6 py-4">
                <span class="text-sm font-medium text-gray-500">Role</span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700">
                    {{ $user->role->name ?? '—' }}
                </span>
            </div>

            <div class="flex items-center justify-between px-6 py-4">
                <span class="text-sm font-medium text-gray-500">Email Verified</span>
                @if($user->email_verified_at)
                    <span class="inline-flex items-center gap-1.5 text-sm text-emerald-600 font-medium">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Verified · {{ $user->email_verified_at->format('d M Y') }}
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 text-sm text-amber-600 font-medium">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                        </svg>
                        Not Verified
                    </span>
                @endif
            </div>

            <div class="flex items-center justify-between px-6 py-4">
                <span class="text-sm font-medium text-gray-500">Account Created</span>
                <span class="text-sm text-gray-800">{{ $user->created_at->format('d M Y, h:i A') }}</span>
            </div>

            <div class="flex items-center justify-between px-6 py-4">
                <span class="text-sm font-medium text-gray-500">Last Updated</span>
                <span class="text-sm text-gray-800">{{ $user->updated_at->format('d M Y, h:i A') }}</span>
            </div>

        </div>

    </div>
</x-app-layout>
