@extends('layouts.admin')

@section('content')
    <div class="max-w-lg mx-auto bg-white p-8 rounded-xl shadow-lg mt-8 border border-blue-900">
        <h2 class="text-2xl font-bold text-blue-900 mb-6">Edit Service</h2>
        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-100 text-red-800 rounded shadow">
                <ul class="list-disc pl-5 text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('admin.service.update', $service->id) }}" method="POST" enctype="multipart/form-data"
            class="space-y-5">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-semibold mb-1 text-blue-900" for="name">Nama Service</label>
                <input type="text" name="name" id="name" value="{{ old('name', $service->name) }}"
                    class="w-full border border-blue-900 bg-white text-blue-900 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 text-sm"
                    required>
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1 text-blue-900" for="description">Deskripsi</label>
                <textarea name="description" id="description" rows="4"
                    class="w-full border border-blue-900 bg-white text-blue-900 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 text-sm"
                    required>{{ old('description', $service->description) }}</textarea>
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" id="is_active" value="1"
                    {{ old('is_active', $service->is_active) ? 'checked' : '' }}>
                <label for="is_active" class="text-xs font-semibold text-blue-900">Aktifkan Service</label>
            </div>
            <div class="flex justify-between items-center mt-6">
                <a href="{{ route('admin.service.index') }}" class="text-blue-700 hover:underline text-sm">&larr;
                    Kembali</a>
                <button type="submit"
                    class="bg-blue-800 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold shadow text-sm">Update</button>
            </div>
        </form>
    </div>
@endsection