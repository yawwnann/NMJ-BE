@php
    $active = fn($route) => request()->routeIs($route) ? 'bg-blue-800 text-white border-l-4 border-blue-400' : 'text-blue-100 hover:bg-blue-800';
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - PT NMJ</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        html {
            font-family: 'Roboto', Arial, sans-serif !important;
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 min-h-screen">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-blue-950 text-white flex flex-col py-6 px-4 min-h-screen shadow-lg sticky top-0">
            <div class="flex flex-col items-center mb-10">
                <img src="{{ asset('logo.png') }}" alt="Logo PT NMJ" class="h-20 w-autoobject-contain p-1 mb-2">
            </div>
            <nav class="flex-1 space-y-1">
                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center gap-3 rounded px-3 py-2 {{$active('admin.dashboard')}} transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 12l2-2m0 0l7-7 7 7M13 5v6h6m-6 0v6m0 0H7m6 0h6" />
                    </svg>
                    Dashboard
                </a>
                <a href="{{ route('admin.hero.index') }}"
                    class="flex items-center gap-3 rounded px-3 py-2 {{$active('admin.hero.*')}} transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Hero Section
                </a>
                <a href="{{ route('admin.service.index') }}"
                    class="flex items-center gap-3 rounded px-3 py-2 {{$active('admin.service.*')}} transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 17v-2a4 4 0 018 0v2m-4-4V7a4 4 0 10-8 0v6m0 0v2a4 4 0 008 0v-2" />
                    </svg>
                    Service
                </a>
                <a href="{{ route('admin.team.index') }}"
                    class="flex items-center gap-3 rounded px-3 py-2 {{$active('admin.team.*')}} transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m9-4a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    Team
                </a>
                <a href="{{ route('admin.project.index') }}"
                    class="flex items-center gap-3 rounded px-3 py-2 {{$active('admin.project.*')}} transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M3 12h18M3 17h18" />
                    </svg>
                    Project
                </a>
            </nav>
            <form method="POST" action="{{ route('logout') }}" class="mt-10">
                @csrf
                <button type="submit"
                    class="w-full bg-blue-800 hover:bg-blue-700 text-white font-semibold py-2 rounded transition">Logout</button>
            </form>
        </aside>
        <!-- Main Content -->
        <main class="flex-1 flex flex-col items-center justify-start p-8 bg-gray-50 min-h-screen">
            <div class="w-full max-w-5xl">
                @yield('content')
            </div>
        </main>
    </div>
</body>

</html>