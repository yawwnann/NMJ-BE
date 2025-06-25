@extends('layouts.admin')

@section('content')
    <div class="max-w-lg mx-auto bg-white p-8 rounded-xl shadow-lg mt-8 border border-blue-900">
        <h2 class="text-2xl font-bold text-blue-900 mb-6">Edit Team</h2>
        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-100 text-red-800 rounded shadow">
                <ul class="list-disc pl-5 text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('admin.team.update', $team->id) }}" method="POST" enctype="multipart/form-data"
            class="space-y-5">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-semibold mb-1 text-blue-900" for="name">Nama</label>
                <input type="text" name="name" id="name" value="{{ old('name', $team->name) }}"
                    class="w-full border border-blue-900 bg-white text-blue-900 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 text-sm"
                    required>
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1 text-blue-900" for="position">Jabatan</label>
                <input type="text" name="position" id="position" value="{{ old('position', $team->position) }}"
                    class="w-full border border-blue-900 bg-white text-blue-900 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 text-sm"
                    required>
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1 text-blue-900" for="phone">No. HP</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone', $team->phone) }}"
                    class="w-full border border-blue-900 bg-white text-blue-900 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 text-sm"
                    required>
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1 text-blue-900" for="email">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email', $team->email) }}"
                    class="w-full border border-blue-900 bg-white text-blue-900 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 text-sm"
                    required>
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1 text-blue-900" for="address">Alamat</label>
                <textarea name="address" id="address" rows="2"
                    class="w-full border border-blue-900 bg-white text-blue-900 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 text-sm"
                    required>{{ old('address', $team->address) }}</textarea>
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1 text-blue-900" for="image">Upload Gambar Baru
                    (Cloudinary)</label>
                <input type="file" name="image" id="image" accept="image/*"
                    class="w-full border border-blue-900 bg-white text-blue-900 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 text-sm">
                @if($team->image_url)
                    <div class="mt-2">
                        <img src="{{ $team->image_url }}" alt="Team Image"
                            class="h-16 w-28 object-cover rounded shadow border border-blue-200">
                    </div>
                @endif
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" id="is_active" value="1"
                    {{ old('is_active', $team->is_active) ? 'checked' : '' }}>
                <label for="is_active" class="text-xs font-semibold text-blue-900">Aktifkan Team</label>
            </div>
            <div class="flex justify-between items-center mt-6">
                <a href="{{ route('admin.team.index') }}" class="text-blue-700 hover:underline text-sm">&larr; Kembali</a>
                <button type="submit"
                    class="bg-blue-800 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold shadow text-sm">Update</button>
            </div>
        </form>
    </div>
@endsection