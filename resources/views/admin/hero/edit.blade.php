@extends('layouts.admin')

@section('content')
    <div class="max-w-xl mx-auto bg-white p-8 rounded shadow">
        <h2 class="text-2xl font-bold text-blue-900 mb-6">Edit Hero Section</h2>
        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('admin.hero.update', $hero) }}" method="POST" enctype="multipart/form-data"
            class="space-y-5">
                @csrf
                @method('PUT')
                <div>
                    <label class="block font-semibold mb-1" for="title">Judul</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $hero->title) }}"
                        class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400" required>
                </div>

                                   <div>
                    <label class="block font-semibold mb-1" for="description">Deskripsi</label>
                    <textarea name="description" id="description" rows="4"
                        class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400"
                        required>{{ old('description', $hero->description) }}</textarea>
                </div>
                <div>
                    <label class="block font-semibold mb-1" for="image">Upload Gambar Baru (Cloudflare)</label>
                    <input type="file" name="image" id="image" accept="image/*" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400">
                    @if($hero->image_url)
                        <div class="mt-2">
                            <span class="text-xs text-gray-500">Gambar saat ini:</span><br>
                            <img src="{{ $hero->image_url }}" alt="Hero Image" class="h-16 w-auto rounded shadow mt-1">
                        </div>
                    @endif
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" id="is_active" value="1"
                        {{ old('is_active', $hero->is_active) ? 'checked' : '' }}>
                    <label for="is_active" class="font-semibold">Aktifkan Hero Section</label>
                </div>
                <div class="flex justify-between items-center mt-6">
                    <a href="{{ route('admin.hero.index') }}" class="text-blue-700 hover:underline">&larr; Kembali</a>
                <button type="submit"
                        class="bg-blue-800 hover:bg-blue-700 text-white px-6 py-2 rounded font-semibold">Update</button>
                </div>
            </form>
        </div>
@endsection