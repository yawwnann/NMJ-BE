@extends('layouts.admin')

@section('content')
    <h2 class="text-xl font-bold text-blue-900 mb-6">Tambah Team</h2>
    <form action="{{ route('admin.team.store') }}" method="POST" class="max-w-xl bg-white rounded shadow p-6"
        enctype="multipart/form-data">
        @csrf
        <div class="mb-4">
            <label class="block font-semibold mb-1">Nama</label>
            <input type="text" name="name" value="{{ old('name') }}"
                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400" required>
            @error('name')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block font-semibold mb-1">Posisi</label>
            <input type="text" name="position" value="{{ old('position') }}"
                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400" required>
            @error('position')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block font-semibold mb-1">No HP</label>
            <input type="text" name="phone" value="{{ old('phone') }}"
                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400" required>
            @error('phone')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block font-semibold mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email') }}"
                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400" required>
            @error('email')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block font-semibold mb-1">Alamat</label>
            <textarea name="address" rows="2"
                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400"
                required>{{ old('address') }}</textarea>
            @error('address')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block font-semibold mb-1">Foto</label>
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
            <a href="{{ route('admin.team.index') }}"
                class="px-4 py-2 rounded border border-gray-300 text-gray-700">Batal</a>
        </div>
    </form>
@endsection