@extends('layouts.admin')

@section('content')
    <div class="flex items-center mb-8">

        <div>
            <h1 class="text-2xl font-bold text-blue-900">Selamat Datang di Dashboard Admin PT NMJ</h1>
            <p class="text-gray-600 mt-1">Kelola konten website Anda dengan mudah.</p>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-8">
        <div class="bg-white rounded shadow p-6 flex flex-col items-center">
            <span class="text-3xl font-bold text-blue-900">{{ \App\Models\HeroSection::count() }}</span>
            <span class="text-gray-600 mt-2">Hero Section</span>
        </div>
        <div class="bg-white rounded shadow p-6 flex flex-col items-center">
            <span class="text-3xl font-bold text-blue-900">{{ \App\Models\Service::count() }}</span>
            <span class="text-gray-600 mt-2">Service</span>
        </div>
        <div class="bg-white rounded shadow p-6 flex flex-col items-center">
            <span class="text-3xl font-bold text-blue-900">{{ \App\Models\Team::count() }}</span>
            <span class="text-gray-600 mt-2">Team</span>
        </div>
        <div class="bg-white rounded shadow p-6 flex flex-col items-center">
            <span class="text-3xl font-bold text-blue-900">{{ \App\Models\Project::count() }}</span>
            <span class="text-gray-600 mt-2">Project</span>
        </div>
    </div>
@endsection