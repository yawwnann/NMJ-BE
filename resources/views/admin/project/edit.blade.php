@extends('layouts.admin')

@section('content')
    <div class="flex justify-center items-start min-h-screen bg-gray-50">
        <div class="w-full max-w-3xl bg-white rounded-2xl shadow-lg p-8 mt-4">
            <h2 class="text-2xl font-bold mb-6 text-blue-900">Edit Project</h2>
            <form id="editProjectForm" method="POST" action="{{ route('admin.project.update', $project->id) }}" enctype="multipart/form-data" class="space-y-6 relative">
                @csrf
                @method('PUT')

                <!-- DEBUG ALERT -->
                <div id="debug-alert" class="hidden mb-4 p-3 bg-yellow-100 text-yellow-800 rounded shadow text-sm"></div>

                <!-- Tombol Update di Atas -->
                <div class="flex justify-end mb-4">
                    <button id="updateBtn" type="submit" class="bg-blue-800 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold shadow text-sm flex items-center gap-2">
                        <span id="btnText">Update Project</span>
                        <svg id="btnSpinner" class="hidden animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg>
                    </button>
                </div>

                @if ($errors->any())
                    <div class="mb-4 p-3 bg-red-100 text-red-800 rounded shadow">
                        <ul class="list-disc pl-5 text-xs">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Informasi Dasar -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold mb-1 text-blue-900" for="title">Judul</label>
                        <input type="text" name="title" id="title" value="{{ old('title', $project->title) }}" class="w-full border border-blue-900 bg-white text-blue-900 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 text-sm @error('title') border-red-500 @enderror" required>
                        @error('title')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1 text-blue-900" for="location">Lokasi</label>
                        <input type="text" name="location" id="location" value="{{ old('location', $project->location) }}" class="w-full border border-blue-900 bg-white text-blue-900 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 text-sm @error('location') border-red-500 @enderror" required>
                        @error('location')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1 text-blue-900" for="description">Deskripsi</label>
                    <textarea name="description" id="description" rows="3" class="w-full border border-blue-900 bg-white text-blue-900 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 text-sm @error('description') border-red-500 @enderror" required>{{ old('description', $project->description) }}</textarea>
                    @error('description')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold mb-1 text-blue-900" for="construction_category">Kategori Konstruksi</label>
                        <select name="construction_category" id="construction_category" class="w-full border border-blue-900 bg-white text-blue-900 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 text-sm @error('construction_category') border-red-500 @enderror" required>
                            <option value="">-- Pilih Kategori --</option>
                            <option value="Gedung" {{ old('construction_category', $project->construction_category) == 'Gedung' ? 'selected' : '' }}>Gedung</option>
                            <option value="Jalan" {{ old('construction_category', $project->construction_category) == 'Jalan' ? 'selected' : '' }}>Jalan</option>
                            <option value="Jembatan" {{ old('construction_category', $project->construction_category) == 'Jembatan' ? 'selected' : '' }}>Jembatan</option>
                            <option value="Renovasi" {{ old('construction_category', $project->construction_category) == 'Renovasi' ? 'selected' : '' }}>Renovasi</option>
                            <option value="Drainase" {{ old('construction_category', $project->construction_category) == 'Drainase' ? 'selected' : '' }}>Drainase</option>
                            <option value="Pabrik" {{ old('construction_category', $project->construction_category) == 'Pabrik' ? 'selected' : '' }}>Pabrik</option>
                            <option value="Perumahan" {{ old('construction_category', $project->construction_category) == 'Perumahan' ? 'selected' : '' }}>Perumahan</option>
                            <option value="Apartemen" {{ old('construction_category', $project->construction_category) == 'Apartemen' ? 'selected' : '' }}>Apartemen</option>
                            <option value="Hotel" {{ old('construction_category', $project->construction_category) == 'Hotel' ? 'selected' : '' }}>Hotel</option>
                            <option value="Rumah Sakit" {{ old('construction_category', $project->construction_category) == 'Rumah Sakit' ? 'selected' : '' }}>Rumah Sakit</option>
                            <option value="Sekolah" {{ old('construction_category', $project->construction_category) == 'Sekolah' ? 'selected' : '' }}>Sekolah</option>
                            <option value="Mall" {{ old('construction_category', $project->construction_category) == 'Mall' ? 'selected' : '' }}>Mall</option>
                            <option value="Bandara" {{ old('construction_category', $project->construction_category) == 'Bandara' ? 'selected' : '' }}>Bandara</option>
                            <option value="Pelabuhan" {{ old('construction_category', $project->construction_category) == 'Pelabuhan' ? 'selected' : '' }}>Pelabuhan</option>
                            <option value="Stadion" {{ old('construction_category', $project->construction_category) == 'Stadion' ? 'selected' : '' }}>Stadion</option>
                            <option value="Lainnya" {{ old('construction_category', $project->construction_category) == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        @error('construction_category')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1 text-blue-900" for="status">Status</label>
                        <select name="status" id="status" class="w-full border border-blue-900 bg-white text-blue-900 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 text-sm @error('status') border-red-500 @enderror" required>
                            <option value="planning" {{ old('status', $project->status) == 'planning' ? 'selected' : '' }}>Planning</option>
                            <option value="in_progress" {{ old('status', $project->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ old('status', $project->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="on_hold" {{ old('status', $project->status) == 'on_hold' ? 'selected' : '' }}>On Hold</option>
                            <option value="cancelled" {{ old('status', $project->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                        @error('status')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
                    </div>
                </div>
                <!-- Tanggal -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold mb-1 text-blue-900" for="start_date">Tanggal Mulai</label>
                        <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $project->start_date) }}" class="w-full border border-blue-900 bg-white text-blue-900 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 text-sm @error('start_date') border-red-500 @enderror" required>
                        @error('start_date')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1 text-blue-900" for="end_date">Tanggal Selesai</label>
                        <input type="date" name="end_date" id="end_date" value="{{ old('end_date', $project->end_date) }}" class="w-full border border-blue-900 bg-white text-blue-900 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 text-sm @error('end_date') border-red-500 @enderror">
                        @error('end_date')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="flex items-center gap-2 mt-2">
                    <input type="checkbox" name="is_ongoing" id="is_ongoing" value="1" {{ old('is_ongoing', $project->is_ongoing) ? 'checked' : '' }}>
                    <label for="is_ongoing" class="text-xs font-semibold text-blue-900">Sampai saat ini</label>
                </div>

                <!-- Preview Gambar Saat Ini -->
                <div class="border-t pt-6">
                    <h3 class="text-lg font-semibold mb-4 text-blue-900">Preview Gambar Saat Ini</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <div class="font-semibold text-xs mb-1">Gambar Utama</div>
                            @if($project->mainImage)
                                <img src="{{ $project->mainImage->image_url }}" alt="Main Image" class="h-24 w-full object-cover rounded shadow border border-blue-200">
                            @else
                                <span class="text-gray-400 text-xs">Belum ada gambar utama</span>
                            @endif
                        </div>
                        <div>
                            <div class="font-semibold text-xs mb-1">Gambar Pekerjaan</div>
                            <div class="flex flex-wrap gap-2">
                                @forelse($project->workImages as $image)
                                    <img src="{{ $image->image_url }}" alt="Work Image" class="h-16 w-24 object-cover rounded shadow border border-green-200">
                                @empty
                                    <span class="text-gray-400 text-xs">Belum ada gambar pekerjaan</span>
                                @endforelse
                            </div>
                        </div>
                        <div>
                            <div class="font-semibold text-xs mb-1">Galeri Gambar</div>
                            <div class="flex flex-wrap gap-2">
                                @forelse($project->galleryImages as $image)
                                    <img src="{{ $image->image_url }}" alt="Gallery Image" class="h-16 w-24 object-cover rounded shadow border border-purple-200">
                                @empty
                                    <span class="text-gray-400 text-xs">Belum ada galeri gambar</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upload Gambar Baru -->
                <div class="border-t pt-6">
                    <h3 class="text-lg font-semibold mb-4 text-blue-900">Upload Gambar Baru</h3>
                    <div class="mb-6">
                        <label class="block text-xs font-semibold mb-2 text-blue-900" for="main_image">
                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">Gambar Utama Baru</span>
                            <span class="text-gray-600 ml-2">(Akan menggantikan gambar utama yang ada)</span>
                        </label>
                        <input type="file" name="main_image" id="main_image" accept="image/*" class="w-full border border-blue-900 bg-white text-blue-900 rounded-lg px-3 py-2">
                    </div>
                    <div class="mb-6">
                        <label class="block text-xs font-semibold mb-2 text-blue-900" for="work_images">
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Gambar Pekerjaan Baru</span>
                        </label>
                        <input type="file" name="work_images[]" id="work_images" accept="image/*" multiple class="w-full border border-blue-900 bg-white text-blue-900 rounded-lg px-3 py-2">
                        <p class="text-xs text-gray-500 mt-1">Pilih beberapa gambar untuk menambahkan ke gambar pekerjaan</p>
                    </div>
                    <div class="mb-6">
                        <label class="block text-xs font-semibold mb-2 text-blue-900" for="gallery_images">
                            <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded text-xs">Galeri Gambar Baru</span>
                        </label>
                        <div id="gallery-inputs">
                            <input type="file" name="gallery_images[]" accept="image/*" class="w-full border border-blue-900 bg-white text-blue-900 rounded-lg px-3 py-2 mb-2">
                        </div>
                        <button type="button" onclick="addGalleryInput()" class="text-blue-700 underline text-xs mb-2">+ Tambahkan Gambar</button>
                        <p class="text-xs text-gray-500 mt-1">Pilih beberapa gambar untuk menambahkan ke galeri</p>
                    </div>
                </div>

                <!-- Status Aktif -->
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $project->is_active) ? 'checked' : '' }}>
                    <label for="is_active" class="text-xs font-semibold text-blue-900">Aktifkan Project</label>
                </div>

                <!-- Tombol Kembali di Bawah -->
                <div class="flex justify-between items-center mt-6">
                    <a href="{{ route('admin.project.index') }}" class="text-blue-700 hover:underline text-sm">&larr; Kembali</a>
                </div>
            </form>
        </div>
    </div>
    <script>
    function addGalleryInput() {
        const container = document.getElementById('gallery-inputs');
        const input = document.createElement('input');
        input.type = 'file';
        input.name = 'gallery_images[]';
        input.accept = 'image/*';
        input.className = 'w-full border border-blue-900 bg-white text-blue-900 rounded-lg px-3 py-2 mb-2';
        container.appendChild(input);
    }

    // Debug: Alert ketika tombol update diklik
    document.getElementById('editProjectForm').addEventListener('submit', function(e) {
        document.getElementById('debug-alert').classList.remove('hidden');
        document.getElementById('debug-alert').innerText = 'DEBUG: Tombol Update Project diklik! Form sedang disubmit...';
        document.getElementById('btnSpinner').classList.remove('hidden');
        document.getElementById('btnText').innerText = 'Menyimpan...';
    });
    </script>
@endsection