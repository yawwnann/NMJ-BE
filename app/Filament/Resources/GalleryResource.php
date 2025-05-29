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
use Filament\Resources\Resource; // Penting: Pastikan ini ada

class GalleryResource extends Resource
{
    // Mengatur model yang akan digunakan oleh resource ini
    protected static ?string $model = Gallery::class;

    // Mengatur ikon navigasi yang akan muncul di sidebar admin Filament
    protected static ?string $navigationIcon = 'heroicon-o-photo'; // Anda bisa ganti ikon ini

    // Mengatur label singular dan plural untuk navigasi (opsional, defaultnya sudah baik)
    protected static ?string $navigationLabel = 'Galeri Foto';
    protected static ?string $pluralModelLabel = 'Galeri Foto';
    protected static ?string $modelLabel = 'Foto Galeri';


    // Definisi skema formulir untuk membuat atau mengedit item galeri
    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                // Input untuk keterangan/caption foto (opsional)
                TextInput::make('caption')
                    ->label('Keterangan Foto')
                    ->maxLength(255)
                    ->nullable()
                    ->placeholder('Contoh: Pembangunan Gedung A'),

                // Upload gambar galeri (wajib)
                FileUpload::make('image')
                    ->label('Gambar Galeri')
                    ->image() // Hanya menerima file gambar
                    ->required() // Wajib diisi
                    ->directory('gallery-images') // Disimpan di storage/app/public/gallery-images
                    ->disk('public') // Menggunakan disk 'public' agar bisa diakses via URL
                    ->imageEditor() // Memungkinkan pengeditan gambar setelah diupload
                    ->columnSpanFull(), // Mengambil lebar penuh kolom formulir
            ]);
    }

    // Definisi kolom tabel untuk menampilkan daftar item galeri
    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                // Kolom untuk menampilkan gambar galeri sebagai thumbnail
                ImageColumn::make('image')
                    ->label('Gambar')
                    ->square(), // Membuat gambar tampil kotak

                // Kolom untuk keterangan foto
                TextColumn::make('caption')
                    ->label('Keterangan')
                    ->searchable() // Bisa dicari
                    ->sortable(), // Bisa diurutkan
            ])
            ->filters([
                // Anda bisa menambahkan filter di sini jika diperlukan
            ])
            ->actions([
                // Aksi untuk mengedit record
                Tables\Actions\EditAction::make(),
                // Aksi untuk menghapus record
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                // Grup aksi massal (misalnya menghapus banyak record sekaligus)
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    // Definisi halaman-halaman yang terkait dengan resource ini
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGalleries::route('/'), // Halaman daftar galeri
            'create' => Pages\CreateGallery::route('/create'), // Halaman tambah galeri
            'edit' => Pages\EditGallery::route('/{record}/edit'), // Halaman edit galeri
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