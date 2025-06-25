@php
    $active = fn($route) => request()->routeIs($route) ? 'bg-blue-800 text-white border-l-4 border-blue-400' : 'text-blue-100 hover:bg-blue-800';
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-blue-950 min-h-screen">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside x-data="{ open: false }"
            class="bg-blue-900 border-r border-blue-800 flex flex-col w-20 md:w-64 transition-all duration-200 shadow-lg fixed inset-y-0 left-0 z-30 h-screen">
            <div class="flex items-center justify-center  border-b border-blue-800">
                <img src="/logo.png" alt="Logo" class="h-20 w-auto md:h-20 md:w-auto">
            </div>
            <nav class="flex-1 py-4 px-2 space-y-2">
                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-blue-100 hover:bg-blue-800 hover:text-white transition">
                    <!-- Heroicon: Home -->
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 12l2-2m0 0l7-7 7 7m-9 2v6a2 2 0 002 2h4a2 2 0 002-2v-6m-5 0h6" />
                    </svg>
                    <span class="hidden md:inline">Dashboard</span>
                </a>
                <a href="{{ route('admin.hero.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-blue-100 hover:bg-blue-800 hover:text-white transition">
                    <!-- Heroicon: Sparkles -->
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M5 3v4M3 5h4m10 12v4m2-2h-4m-7-7l2 2m0-2l-2 2m7-7l2 2m0-2l-2 2" />
                    </svg>
                    <span class="hidden md:inline">Hero</span>
                </a>
                <a href="{{ route('admin.service.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-blue-100 hover:bg-blue-800 hover:text-white transition">
                    <!-- Heroicon: Briefcase -->
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M6 7V6a2 2 0 012-2h8a2 2 0 012 2v1m-2 4h2a2 2 0 012 2v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5a2 2 0 012-2h2m2 0V6a2 2 0 012-2h0a2 2 0 012 2v5" />
                    </svg>
                    <span class="hidden md:inline">Service</span>
                </a>
                <a href="{{ route('admin.team.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-blue-100 hover:bg-blue-800 hover:text-white transition">
                    <!-- Heroicon: Users -->
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87M16 3.13a4 4 0 010 7.75M8 3.13a4 4 0 010 7.75" />
                    </svg>
                    <span class="hidden md:inline">Team</span>
                </a>
                <a href="{{ route('admin.project.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-blue-100 hover:bg-blue-800 hover:text-white transition">
                    <!-- Heroicon: Collection -->
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M19 11H5m14 4H5m14-8H5m2 4v6m0-6V7m0 6h6m-6 0h6" />
                    </svg>
                    <span class="hidden md:inline">Project</span>
                </a>
            </nav>
            <div class="mt-auto p-4 hidden md:block">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-2 px-3 py-2 rounded-lg bg-blue-800 text-white hover:bg-blue-700 transition font-semibold">
                        <!-- Heroicon: Logout -->
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2h4a2 2 0 012 2v1" />
                        </svg>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </aside>
        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-h-screen ml-20 md:ml-64">
            <!-- Header -->
            <header
                class="sticky top-0 z-10 bg-blue-900 border-b border-blue-800 h-20 flex items-center justify-between px-6 shadow-sm">
                <div class="flex items-center gap-2">
                    <button @click="open = !open" class="md:hidden text-blue-100 hover:text-white focus:outline-none">
                        <!-- Heroicon: Menu -->
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <span class="font-bold text-lg text-blue-100">Admin Panel</span>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-blue-100 font-semibold">{{ Auth::user()->name ?? 'Admin' }}</span>
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'A') }}&background=1e40af&color=fff&size=32"
                        class="rounded-full h-8 w-8 border-2 border-blue-300" alt="User Avatar">
                </div>
            </header>
            <!-- Content -->
            <main class="flex-1 p-4 md:p-8 bg-white min-h-screen">
                <div class="max-w-7xl mx-auto">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
</body>

</html>