@extends('layouts.admin')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-blue-900">Hero Section</h2>
        <a href="{{ route('admin.hero.create') }}"
            class="bg-blue-800 hover:bg-blue-700 text-white px-4 py-2 rounded font-semibold shadow">+ Tambah Hero</a>
    </div>
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded shadow">{{ session('success') }}</div>
    @endif
    <div class="bg-white rounded-xl shadow-lg overflow-x-auto border border-blue-900">
        <table class="min-w-full text-xs md:text-sm">
            <thead class="bg-blue-900 text-white">
                <tr>
                    <th class="px-4 py-3 font-semibold">#</th>
                    <th class="px-4 py-3 font-semibold">Judul</th>
                    <th class="px-4 py-3 font-semibold">Deskripsi</th>
                    <th class="px-4 py-3 font-semibold">Gambar</th>
                    <th class="px-4 py-3 font-semibold">Status</th>
                    <th class="px-4 py-3 font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($heros as $hero)
                    <tr class="border-b border-blue-100 hover:bg-blue-50 transition">
                        <td class="px-4 py-3 text-blue-900">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3 font-semibold text-blue-900">{{ $hero->title }}</td>
                        <td class="px-4 py-3 text-blue-900">{{ Str::limit($hero->description, 50) }}</td>
                        <td class="px-4 py-3">
                            @if($hero->image_url)
                                <img src="{{ $hero->image_url }}" alt="Hero Image"
                                    class="h-12 w-20 object-cover rounded shadow border border-blue-200">
                            @else
                                <span class="text-blue-300">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($hero->is_active)
                                <span class="bg-green-200 text-green-800 px-2 py-1 rounded text-xs">Aktif</span>
                            @else
                                <span class="bg-gray-200 text-gray-800 px-2 py-1 rounded text-xs">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 flex gap-2">
                            <a href="{{ route('admin.hero.edit', $hero) }}"
                                class="bg-yellow-600 hover:bg-yellow-700 text-white px-3 py-1 rounded text-xs font-semibold shadow">Edit</a>
                            <form action="{{ route('admin.hero.destroy', $hero) }}" method="POST"
                                onsubmit="return confirm('Yakin hapus data ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="bg-red-800 hover:bg-red-900 text-white px-3 py-1 rounded text-xs font-semibold shadow">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-8 text-blue-300">Belum ada data hero section.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection