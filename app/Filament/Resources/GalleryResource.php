<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GalleryResource\Pages;
use App\Filament\Resources\GalleryResource\RelationManagers;
use App\Models\Gallery; // Penting: Pastikan ini mengarah ke model Gallery Anda
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Resources\Resource;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary; // <-- Tambahkan ini untuk menggunakan Facade Cloudinary
use Illuminate\Support\Facades\Log; // <-- Tambahkan ini untuk logging
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile; // <-- Tambahkan ini untuk tipe hint yang jelas

class GalleryResource extends Resource
{
    // Mengatur model yang akan digunakan oleh resource ini
    protected static ?string $model = Gallery::class;

    // Mengatur ikon navigasi yang akan muncul di sidebar admin Filament
    protected static ?string $navigationIcon = 'heroicon-o-photo'; // Anda bisa ganti ikon ini

    // Mengatur label singular dan plural untuk navigasi
    protected static ?string $navigationLabel = 'Galeri Foto';
    protected static ?string $pluralModelLabel = 'Galeri Foto';
    protected static ?string $modelLabel = 'Foto Galeri';

    // Definisi skema formulir untuk membuat atau mengedit item galeri
    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                TextInput::make('caption')
                    ->label('Keterangan Foto')
                    ->maxLength(255)
                    ->nullable()
                    ->placeholder('Contoh: Pembangunan Gedung A'),

                // Gambar Galeri - Menggunakan saveUploadedFileUsing untuk Cloudinary
                FileUpload::make('image')
                    ->label('Gambar Galeri')
                    ->image() // Hanya menerima file gambar
                    ->required() // Wajib diisi
                    ->imageEditor() // Memungkinkan pengeditan gambar
                    ->columnSpanFull() // Mengambil lebar penuh kolom formulir
                    ->saveUploadedFileUsing(function (Forms\Components\FileUpload $component, TemporaryUploadedFile $file): ?string {
                        // Pastikan file sementara ada sebelum diproses
                        if (!$file->exists()) {
                            Log::error('Temporary file for Gallery upload does not exist: ' . $file->getFilename());
                            throw new \Exception('Temporary file not found for upload.');
                        }

                        try {
                            // Dapatkan path fisik dari file sementara
                            $realPath = $file->getRealPath();
                            if (!$realPath) {
                                Log::error('Temporary file has no real path for Gallery upload: ' . $file->getFilename());
                                throw new \Exception('Temporary file has no real path.');
                            }

                            $options = [
                                'folder' => 'company-profile/gallery', // Tentukan folder di Cloudinary
                                'public_id' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '_' . uniqid(), // Nama file unik
                            ];

                            // Logging sebelum upload ke Cloudinary
                            Log::info('Attempting Cloudinary upload for Gallery file: ' . $file->getClientOriginalName() . ' from path: ' . $realPath);

                            // Lakukan proses upload ke Cloudinary
                            $uploadedFile = Cloudinary::upload($realPath, $options);

                            // --- Logging hasil upload dari Cloudinary ---
                            if ($uploadedFile) {
                                // Jika upload berhasil, periksa apakah secure path tersedia
                                if ($uploadedFile->getSecurePath()) {
                                    Log::info('Cloudinary upload successful for Gallery. Public ID: ' . $uploadedFile->getPublicId() . ' URL: ' . $uploadedFile->getSecurePath());
                                    return $uploadedFile->getSecurePath(); // Mengembalikan URL HTTPS
                                } else {
                                    // Jika tidak ada secure path meskipun upload berhasil
                                    Log::error('Cloudinary upload successful but no secure path found for Gallery. Full result: ' . json_encode($uploadedFile->toArray()));
                                    throw new \Exception('Cloudinary upload successful but no secure path returned.');
                                }
                            } else {
                                // Jika Cloudinary::upload() mengembalikan null
                                Log::error('Cloudinary upload returned NULL for Gallery file: ' . $file->getClientOriginalName());
                                throw new \Exception('Cloudinary upload failed: returned null. Check Cloudinary credentials.');
                            }
                            // --- Akhir logging hasil upload ---
            
                        } catch (\Exception $e) {
                            // Tangkap error dari Cloudinary API atau error umum lainnya
                            Log::error('Cloudinary Upload Error (Gallery): ' . $e->getMessage() . ' - File: ' . $file->getClientOriginalName());
                            throw new \Exception('Failed to upload image to Cloudinary: ' . $e->getMessage());
                        }
                    }),
            ]);
    }

    // Definisi kolom tabel untuk menampilkan daftar item galeri
    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Gambar')
                    ->square(), // Membuat gambar tampil kotak
                TextColumn::make('caption')
                    ->label('Keterangan')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                // Tambahkan filter di sini jika diperlukan
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    // Definisi halaman-halaman yang terkait dengan resource ini
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGalleries::route('/'),
            'create' => Pages\CreateGallery::route('/create'),
            'edit' => Pages\EditGallery::route('/{record}/edit'), // Pastikan ini benar (EditGallery)
        ];
    }

    // Definisi relasi yang terkait dengan resource ini (jika ada)
    public static function getRelations(): array
    {
        return [
            // Contoh: RelationManagers\SomeRelationManager::class,
        ];
    }
}