<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] min-h-screen">
    <nav class="bg-white dark:bg-[#161615] border-b border-[#e3e3e0] dark:border-[#3E3E3A]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ url('/') }}" class="text-lg font-semibold">
                        {{ config('app.name', 'Laravel') }}
                    </a>
                </div>
                <div class="flex items-center gap-4">
                    @auth
                        <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">
                            {{ auth()->user()->username ?? 'User' }}
                        </span>
                        @if (Route::has('logout'))
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="text-sm px-4 py-2 border border-[#19140035] dark:border-[#3E3E3A] hover:border-[#1915014a] dark:hover:border-[#62605b] rounded-sm">
                                    Logout
                                </button>
                            </form>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="text-sm px-4 py-2 border border-transparent hover:border-[#19140035] dark:hover:border-[#3E3E3A] rounded-sm">
                            Log in
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="text-sm px-4 py-2 border border-[#19140035] dark:border-[#3E3E3A] hover:border-[#1915014a] dark:hover:border-[#62605b] rounded-sm">
                                Register
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-sm">
                <p class="text-sm text-green-800 dark:text-green-300">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-sm">
                <p class="text-sm text-red-800 dark:text-red-300">{{ session('error') }}</p>
            </div>
        @endif
    </div>

    <main class="py-8">
        @yield('content')
    </main>
</body>
</html>

