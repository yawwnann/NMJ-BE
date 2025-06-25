@extends('layouts.admin')

@section('content')
    <h2 class="text-xl font-bold text-blue-900 mb-6">Edit Service</h2>
    <form action="{{ route('admin.service.update', $service) }}" method="POST" class="max-w-xl bg-white rounded shadow p-6"
        enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label class="block font-semibold mb-1">Judul</label>
            <input type="text" name="title" value="{{ old('title', $service->title) }}"
                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400" required>
            @error('title')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block font-semibold mb-1">Deskripsi</label>
            <textarea name="description" rows="4"
                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400"
                required>{{ old('description', $service->description) }}</textarea>
            @error('description')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="inline-flex items-center">
                <input type="checkbox" name="is_active" value="1" class="mr-2"
                    {{ old('is_active', $service->is_active) ? 'checked' : '' }}>
                Aktif
            </label>
        </div>
        <div class="flex gap-2">
            <button type="submit"
                class="bg-blue-800 hover:bg-blue-700 text-white px-4 py-2 rounded font-semibold">Update</button>
            <a href="{{ route('admin.service.index') }}"
                class="px-4 py-2 rounded border border-gray-300 text-gray-700">Batal</a>
        </div>
    </form>
@endsection