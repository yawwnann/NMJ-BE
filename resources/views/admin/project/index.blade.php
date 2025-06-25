@extends('layouts.admin')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-blue-900">Project</h2>
        <a href="{{ route('admin.project.create') }}"
            class="bg-blue-800 hover:bg-blue-700 text-white px-4 py-2 rounded font-semibold">+ Tambah Project</a>
    </div>
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
    @endif
    <table class="min-w-full bg-white rounded shadow overflow-hidden">
        <thead class="bg-blue-900 text-white">
            <tr>
                <th class="px-4 py-2">#</th>
                <th class="px-4 py-2">Judul</th>
                <th class="px-4 py-2">Lokasi</th>
                <th class="px-4 py-2">Deskripsi</th>
                <th class="px-4 py-2">Kategori</th>
                <th class="px-4 py-2">Mulai</th>
                <th class="px-4 py-2">Selesai</th>
                <th class="px-4 py-2">Durasi</th>
                <th class="px-4 py-2">Status</th>
                <th class="px-4 py-2">Gambar</th>
                <th class="px-4 py-2">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($projects as $project)
                <tr class="border-b hover:bg-blue-50">
                    <td class="px-4 py-2">{{ $loop->iteration }}</td>
                    <td class="px-4 py-2 font-semibold">{{ $project->title }}</td>
                    <td class="px-4 py-2">{{ $project->location }}</td>
                    <td class="px-4 py-2">{{ Str::limit($project->description, 40) }}</td>
                    <td class="px-4 py-2">{{ $project->construction_category }}</td>
                    <td class="px-4 py-2">{{ $project->start_date }}</td>
                    <td class="px-4 py-2">{{ $project->is_ongoing ? 'Sampai saat ini' : $project->end_date }}</td>
                    <td class="px-4 py-2">
                        @php
                            if ($project->is_ongoing || !$project->end_date) {
                                echo '-';
                            } else {
                                $start = \Carbon\Carbon::parse($project->start_date);
                                $end = \Carbon\Carbon::parse($project->end_date);
                                $days = $start->diffInDays($end) + 1;
                                $months = floor($days / 30);
                                echo $months > 0 ? $months . ' bulan ' . ($days % 30) . ' hari' : $days . ' hari';
                            }
                        @endphp
                    </td>
                    <td class="px-4 py-2">
                        <span class="capitalize">{{ str_replace('_', ' ', $project->status) }}</span>
                    </td>
                    <td class="px-4 py-2">
                        @if($project->image_url)
                            <img src="{{ $project->image_url }}" alt="Gambar" class="h-12 w-20 object-cover rounded shadow">
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-4 py-2 flex gap-2">
                        <a href="{{ route('admin.project.edit', $project) }}"
                            class="bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded text-xs">Edit</a>
                        <form action="{{ route('admin.project.destroy', $project) }}" method="POST"
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
                    <td colspan="9" class="text-center py-8 text-gray-400">Belum ada data project.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection