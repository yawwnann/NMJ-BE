@extends('layouts.admin')

@section('content')
    <h2 class="text-xl font-bold text-blue-900 mb-6">Tambah Project</h2>
    <form action="{{ route('admin.project.store') }}" method="POST" class="max-w-xl bg-white rounded shadow p-6"
        enctype="multipart/form-data">
        @csrf
        <div class="mb-4">
            <label class="block font-semibold mb-1">Judul</label>
            <input type="text" name="title" value="{{ old('title') }}"
                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400" required>
            @error('title')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block font-semibold mb-1">Lokasi</label>
            <input type="text" name="location" value="{{ old('location') }}"
                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400" required>
            @error('location')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block font-semibold mb-1">Deskripsi</label>
            <textarea name="description" rows="3"
                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400"
                required>{{ old('description') }}</textarea>
            @error('description')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block font-semibold mb-1">Kategori</label>
            <input type="text" name="category" value="{{ old('category') }}"
                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400" required>
            @error('category')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block font-semibold mb-1">Durasi</label>
            <input type="text" name="duration" value="{{ old('duration') }}"
                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400" required>
            @error('duration')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block font-semibold mb-1">Status</label>
            <select name="status"
                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400" required>
                <option value="planning" {{ old('status') == 'planning' ? 'selected' : '' }}>Planning</option>
                <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="on_hold" {{ old('status') == 'on_hold' ? 'selected' : '' }}>On Hold</option>
                <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
            @error('status')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block font-semibold mb-1">Gambar</label>
            <input type="file" name="image" accept="image/*" class="w-full border rounded px-3 py-2">
            @error('image')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="inline-flex items-center">
                <input type="checkbox" name="is_active" value="1" class="mr-2"
                    {{ old('is_active', true) ? 'checked' : '' }}>
                Aktif
            </label>
        </div>
        <div class="flex gap-2">
            <button type="submit"
                class="bg-blue-800 hover:bg-blue-700 text-white px-4 py-2 rounded font-semibold">Simpan</button>
            <a href="{{ route('admin.project.index') }}"
                class="px-4 py-2 rounded border border-gray-300 text-gray-700">Batal</a>
        </div>
    </form>
@endsection