@extends('layouts.admin')

@section('content')
    <div class="flex justify-center items-start min-h-screen bg-gray-50">
        <div class="w-full max-w-4xl bg-white rounded-2xl shadow-lg p-8 mt-4">
            <h2 class="text-2xl font-bold mb-6 text-blue-900">Tambah Project</h2>
            @if ($errors->any())
                <div class="mb-4 p-3 bg-red-100 text-red-800 rounded shadow">
                    <ul class="list-disc pl-5 text-xs">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form method="POST" action="{{ route('admin.project.store') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Basic Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold mb-1 text-blue-900" for="title">Judul</label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}"
                            class="w-full border border-blue-900 bg-white text-blue-900 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 text-sm"
                            required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1 text-blue-900" for="location">Lokasi</label>
                        <input type="text" name="location" id="location" value="{{ old('location') }}"
                            class="w-full border border-blue-900 bg-white text-blue-900 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 text-sm"
                            required>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold mb-1 text-blue-900" for="description">Deskripsi</label>
                    <textarea name="description" id="description" rows="4"
                        class="w-full border border-blue-900 bg-white text-blue-900 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 text-sm"
                        required>{{ old('description') }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold mb-1 text-blue-900" for="construction_category">Kategori
                            Konstruksi</label>
                        <select name="construction_category" id="construction_category"
                            class="w-full border border-blue-900 bg-white text-blue-900 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 text-sm"
                            required>
                            <option value="">-- Pilih Kategori --</option>
                            <option value="Gedung" {{ old('construction_category') == 'Gedung' ? 'selected' : '' }}>Gedung
                            </option>
                            <option value="Jalan" {{ old('construction_category') == 'Jalan' ? 'selected' : '' }}>Jalan
                            </option>
                            <option value="Jembatan" {{ old('construction_category') == 'Jembatan' ? 'selected' : '' }}>
                                Jembatan</option>
                            <option value="Renovasi" {{ old('construction_category') == 'Renovasi' ? 'selected' : '' }}>
                                Renovasi</option>
                            <option value="Drainase" {{ old('construction_category') == 'Drainase' ? 'selected' : '' }}>
                                Drainase</option>
                            <option value="Pabrik" {{ old('construction_category') == 'Pabrik' ? 'selected' : '' }}>Pabrik
                            </option>
                            <option value="Perumahan" {{ old('construction_category') == 'Perumahan' ? 'selected' : '' }}>
                                Perumahan</option>
                            <option value="Apartemen" {{ old('construction_category') == 'Apartemen' ? 'selected' : '' }}>
                                Apartemen</option>
                            <option value="Hotel" {{ old('construction_category') == 'Hotel' ? 'selected' : '' }}>Hotel
                            </option>
                            <option value="Rumah Sakit"
                                {{ old('construction_category') == 'Rumah Sakit' ? 'selected' : '' }}>Rumah Sakit</option>
                            <option value="Sekolah" {{ old('construction_category') == 'Sekolah' ? 'selected' : '' }}>
                                Sekolah</option>
                            <option value="Mall" {{ old('construction_category') == 'Mall' ? 'selected' : '' }}>Mall
                            </option>
                            <option value="Bandara" {{ old('construction_category') == 'Bandara' ? 'selected' : '' }}>
                                Bandara</option>
                            <option value="Pelabuhan" {{ old('construction_category') == 'Pelabuhan' ? 'selected' : '' }}>
                                Pelabuhan</option>
                            <option value="Stadion" {{ old('construction_category') == 'Stadion' ? 'selected' : '' }}>
                                Stadion</option>
                            <option value="Lainnya" {{ old('construction_category') == 'Lainnya' ? 'selected' : '' }}>
                                Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1 text-blue-900" for="status">Status</label>
                        <select name="status" id="status"
                            class="w-full border border-blue-900 bg-white text-blue-900 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 text-sm"
                            required>
                            <option value="planning" {{ old('status') == 'planning' ? 'selected' : '' }}>Planning</option>
                            <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>In Progress
                            </option>
                            <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed
                            </option>
                            <option value="on_hold" {{ old('status') == 'on_hold' ? 'selected' : '' }}>On Hold</option>
                            <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled
                            </option>
                        </select>
                    </div>
                </div>

                <!-- Date Range -->
                <div x-data="{
                            start: '',
                            end: '',
                            ongoing: false,
                            get duration() {
                                if (!this.start) return '';
                                if (this.ongoing || !this.end) return '';
                                const start = new Date(this.start);
                                const end = new Date(this.end);
                                if (isNaN(start) || isNaN(end) || end < start) return '';
                                const diff = end - start;
                                const days = Math.floor(diff / (1000 * 60 * 60 * 24)) + 1;
                                const months = Math.floor(days / 30);
                                return months > 0 ? `${months} bulan ${days % 30} hari` : `${days} hari`;
                            }
                        }" x-init="start = $refs.start.value; end = $refs.end.value; ongoing = $refs.ongoing.checked">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold mb-1 text-blue-900" for="start_date">Tanggal
                                Mulai</label>
                            <input x-ref="start" x-model="start" type="date" name="start_date" id="start_date"
                                value="{{ old('start_date') }}"
                                class="w-full border border-blue-900 bg-white text-blue-900 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 text-sm"
                                required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold mb-1 text-blue-900" for="end_date">Tanggal
                                Selesai</label>
                            <input x-ref="end" x-model="end" :disabled="ongoing" type="date" name="end_date" id="end_date"
                                value="{{ old('end_date') }}"
                                class="w-full border border-blue-900 bg-white text-blue-900 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 text-sm">
                        </div>
                    </div>
                    <div class="flex items-center gap-2 mt-2">
                        <input x-ref="ongoing" x-model="ongoing" type="checkbox" name="is_ongoing" id="is_ongoing" value="1"
                            {{ old('is_ongoing') ? 'checked' : '' }}>
                        <label for="is_ongoing" class="text-xs font-semibold text-blue-900">Sampai saat ini</label>
                    </div>
                    <div class="mt-2 text-xs text-blue-900 font-semibold" x-text="duration ? 'Durasi: ' + duration : ''">
                    </div>
                </div>

                <!-- Image Upload Section -->
                <div class="border-t pt-6">
                    <h3 class="text-lg font-semibold mb-4 text-blue-900">Upload Gambar</h3>

                    <!-- Main Image -->
                    <div class="mb-6">
                        <label class="block text-xs font-semibold mb-2 text-blue-900" for="main_image">
                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">Gambar Utama</span>
                            <span class="text-gray-600 ml-2">(Untuk ditampilkan di halaman utama)</span>
                        </label>
                        <input type="file" name="main_image" id="main_image" accept="image/*"
                            class="w-full border border-blue-900 bg-white text-blue-900 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 text-sm">
                        <p class="text-xs text-gray-500 mt-1">Gambar ini akan ditampilkan sebagai thumbnail utama project
                        </p>
                    </div>

                    <!-- Work Images -->
                    <div class="mb-6">
                        <label class="block text-xs font-semibold mb-2 text-blue-900" for="work_images">
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Gambar Pekerjaan</span>
                            <span class="text-gray-600 ml-2">(Untuk bagian yang sedang dikerjakan)</span>
                        </label>
                        <input type="file" name="work_images[]" id="work_images" accept="image/*" multiple
                            class="w-full border border-blue-900 bg-white text-blue-900 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 text-sm">
                        <p class="text-xs text-gray-500 mt-1">Pilih beberapa gambar untuk menampilkan progress pekerjaan</p>
                    </div>

                    <!-- Gallery Images -->
                    <div class="mb-6">
                        <label class="block text-xs font-semibold mb-2 text-blue-900">
                            <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded text-xs">Galeri Gambar</span>
                            <span class="text-gray-600 ml-2">(Gambar tambahan untuk galeri)</span>
                        </label>
                        <div id="gallery-inputs">
                            <input type="file" name="gallery_images[]" accept="image/*"
                                class="w-full border border-blue-900 bg-white text-blue-900 rounded-lg px-3 py-2 mb-2">
                        </div>
                        <button type="button" onclick="addGalleryInput()" class="text-blue-700 underline text-xs mb-2">+
                            Tambahkan Gambar</button>
                        <p class="text-xs text-gray-500 mt-1">Gambar tambahan untuk galeri project</p>
                    </div>
                </div>

                <!-- Active Status -->
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" id="is_active" value="1"
                        {{ old('is_active') ? 'checked' : '' }}>
                    <label for="is_active" class="text-xs font-semibold text-blue-900">Aktifkan Project</label>
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-between items-center mt-6">
                    <a href="{{ route('admin.project.index') }}" class="text-blue-700 hover:underline text-sm">&larr;
                        Kembali</a>
                    <button type="submit"
                        class="bg-blue-800 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold shadow text-sm">Simpan
                        Project</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        function addGalleryInput() {
            const container=document.getElementById('gallery-inputs');
            const input=document.createElement('input');
            input.type='file';
            input.name='gallery_images[]';
            input.accept='image/*';
            input.className='w-full border border-blue-900 bg-white text-blue-900 rounded-lg px-3 py-2 mb-2';
            container.appendChild(input);
        }
    </script>
@endsection