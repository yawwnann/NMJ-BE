@extends('layouts.admin')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-blue-900">Team</h2>
        <a href="{{ route('admin.team.create') }}"
            class="bg-blue-800 hover:bg-blue-700 text-white px-4 py-2 rounded font-semibold">+ Tambah Team</a>
    </div>
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
    @endif
    <table class="min-w-full bg-white rounded shadow overflow-hidden">
        <thead class="bg-blue-900 text-white">
            <tr>
                <th class="px-4 py-2">#</th>
                <th class="px-4 py-2">Nama</th>
                <th class="px-4 py-2">Posisi</th>
                <th class="px-4 py-2">No HP</th>
                <th class="px-4 py-2">Email</th>
                <th class="px-4 py-2">Alamat</th>
                <th class="px-4 py-2">Foto</th>
                <th class="px-4 py-2">Status</th>
                <th class="px-4 py-2">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($teams as $team)
                <tr class="border-b hover:bg-blue-50">
                    <td class="px-4 py-2">{{ $loop->iteration }}</td>
                    <td class="px-4 py-2 font-semibold">{{ $team->name }}</td>
                    <td class="px-4 py-2">{{ $team->position }}</td>
                    <td class="px-4 py-2">{{ $team->phone }}</td>
                    <td class="px-4 py-2">{{ $team->email }}</td>
                    <td class="px-4 py-2">{{ Str::limit($team->address, 30) }}</td>
                    <td class="px-4 py-2">
                        @if($team->image_url)
                            <img src="{{ $team->image_url }}" alt="Foto" class="h-12 w-12 object-cover rounded-full shadow">
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-4 py-2">
                        @if($team->is_active)
                            <span class="bg-green-200 text-green-800 px-2 py-1 rounded text-xs">Aktif</span>
                        @else
                            <span class="bg-gray-200 text-gray-800 px-2 py-1 rounded text-xs">Nonaktif</span>
                        @endif
                    </td>
                    <td class="px-4 py-2 flex gap-2">
                        <a href="{{ route('admin.team.edit', $team) }}"
                            class="bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded text-xs">Edit</a>
                        <form action="{{ route('admin.team.destroy', $team) }}" method="POST"
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
                    <td colspan="9" class="text-center py-8 text-gray-400">Belum ada data team.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection