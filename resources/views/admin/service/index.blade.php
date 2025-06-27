@extends('layouts.admin')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-blue-900">Service</h2>
        <a href="{{ route('admin.service.create') }}"
            class="bg-blue-800 hover:bg-blue-700 text-white px-4 py-2 rounded font-semibold">+ Tambah Service</a>
    </div>
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
    @endif
    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full text-xs md:text-sm">
            <thead class="bg-blue-900 text-white">
                <tr>
                    <th class="px-4 py-2">#</th>
                    <th class="px-4 py-2">Judul</th>
                    <th class="px-4 py-2">Deskripsi</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($services as $service)
                    <tr class="border-b hover:bg-blue-50">
                        <td class="px-4 py-2">{{ $loop->iteration }}</td>
                        <td class="px-4 py-2 font-semibold">{{ $service->title }}</td>
                        <td class="px-4 py-2">{{ Str::limit($service->description, 50) }}</td>
                        <td class="px-4 py-2">
                            @if($service->is_active)
                                <span class="bg-green-200 text-green-800 px-2 py-1 rounded text-xs">Aktif</span>
                            @else
                                <span class="bg-gray-200 text-gray-800 px-2 py-1 rounded text-xs">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 flex gap-2">
                            <a href="{{ route('admin.service.edit', $service) }}"
                                class="bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded text-xs">Edit</a>
                            <form action="{{ route('admin.service.destroy', $service) }}" method="POST"
                                onsubmit="return confirm('Yakin hapus data ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-8 text-gray-400">Belum ada data service.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection