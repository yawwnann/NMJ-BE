@extends('layouts.admin')

@section('content')
    <h2 class="text-xl font-bold text-blue-900 mb-6">Edit Project</h2>
    <form action="{{ route('admin.project.update', $project) }}" method="POST" class="max-w-xl bg-white rounded shadow p-6"
        enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label class="block font-semibold mb-1">Judul</label>
            <input type="text" name="title" value="{{ old('title', $project->title) }}"
                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400" required>
            @error('title')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block font-semibold mb-1">Lokasi</label>
            <input type="text" name="location" value="{{ old('location', $project->location) }}"
                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400" required>
            @error('location')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block font-semibold mb-1">Deskripsi</label>
            <textarea name="description" rows="3"
                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400"
                required>{{ old('description', $project->description) }}</textarea>
            @error('description')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block font-semibold mb-1">Kategori</label>
            <input type="text" name="category" value="{{ old('category', $project->category) }}"
                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400" required>
            @error('category')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block font-semibold mb-1">Durasi</label>
            <input type="text" name="duration" value="{{ old('duration', $project->duration) }}"
                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400" required>
            @error('duration')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block font-semibold mb-1">Status</label>
            <select name="status"
                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400" required>
                <option value="planning" {{ old('status', $project->status) == 'planning' ? 'selected' : '' }}>Planning
                </option>
                <option value="in_progress" {{ old('status', $project->status) == 'in_progress' ? 'selected' : '' }}>In
                    Progress</option>
                <option value="completed" {{ old('status', $project->status) == 'completed' ? 'selected' : '' }}>Completed
                </option>
                <option value="on_hold" {{ old('status', $project->status) == 'on_hold' ? 'selected' : '' }}>On Hold
                </option>
                <option value="cancelled" {{ old('status', $project->status) == 'cancelled' ? 'selected' : '' }}>Cancelled
                </option>
            </select>
            @error('status')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block font-semibold mb-1">Gambar</label>
            <input type="file" name="image" accept="image/*" class="w-full border rounded px-3 py-2">
            @if($project->image_url)
                <img src="{{ $project->image_url }}" alt="Gambar" class="h-16 w-28 mt-2 rounded shadow">
            @endif
            @error('image')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="inline-flex items-center">
                <input type="checkbox" name="is_active" value="1" class="mr-2"
                    {{ old('is_active', $project->is_active) ? 'checked' : '' }}>
                Aktif
            </label>
        </div>
        <div class="flex gap-2">
            <button type="submit"
                class="bg-blue-800 hover:bg-blue-700 text-white px-4 py-2 rounded font-semibold">Update</button>
            <a href="{{ route('admin.project.index') }}"
                class="px-4 py-2 rounded border border-gray-300 text-gray-700">Batal</a>
        </div>
    </form>
@endsection