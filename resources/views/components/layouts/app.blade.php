<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="theme-color" content="#1e1b4b">
    <title>Stash</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full bg-gray-50 font-sans antialiased">
    <div class="flex h-full flex-col">
        {{-- Header --}}
        <header class="bg-indigo-900 px-4 pt-safe-top pb-3 text-white">
            <div class="flex items-center justify-between">
                <a href="{{ route('search') }}" class="text-lg font-bold tracking-tight">
                    Stash
                </a>
                <a href="{{ route('items.create') }}" class="rounded-full bg-indigo-700 px-3 py-1 text-sm font-medium hover:bg-indigo-600">
                    + Add Item
                </a>
            </div>
        </header>

        {{-- Main content --}}
        <main class="flex-1 overflow-y-auto pb-20">
            {{ $slot }}
        </main>

        {{-- Bottom nav --}}
        <nav class="fixed bottom-0 left-0 right-0 z-50 border-t border-gray-200 bg-white pb-safe-bottom">
            <div class="grid grid-cols-3">
                <a href="{{ route('search') }}" class="flex flex-col items-center gap-1 py-2 text-xs font-medium {{ request()->routeIs('search') ? 'text-indigo-700' : 'text-gray-500' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                    </svg>
                    Search
                </a>
                <a href="{{ route('items.index') }}" class="flex flex-col items-center gap-1 py-2 text-xs font-medium {{ request()->routeIs('items.*') ? 'text-indigo-700' : 'text-gray-500' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2zM16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                    </svg>
                    Items
                </a>
                <a href="{{ route('locations.index') }}" class="flex flex-col items-center gap-1 py-2 text-xs font-medium {{ request()->routeIs('locations.*') ? 'text-indigo-700' : 'text-gray-500' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/>
                    </svg>
                    Locations
                </a>
            </div>
        </nav>
    </div>

    @livewireScripts
</body>
</html>
