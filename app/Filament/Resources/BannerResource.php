<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BannerResource\Pages;
use App\Filament\Resources\BannerResource\RelationManagers;
use App\Models\Banner; // Pastikan ini mengarah ke model Banner Anda
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Resources\Resource;

class BannerResource extends Resource
{
    // Mengatur model yang akan digunakan oleh resource ini
    protected static ?string $model = Banner::class;

    // Mengatur ikon navigasi yang akan muncul di sidebar admin Filament
    protected static ?string $navigationIcon = 'heroicon-o-photo';

    // Mengatur label singular dan plural untuk navigasi (opsional, defaultnya sudah baik)
    protected static ?string $navigationLabel = 'Banners';
    protected static ?string $pluralModelLabel = 'Banners';
    protected static ?string $modelLabel = 'Banner';


    // Definisi skema formulir untuk membuat atau mengedit banner
    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                // Input untuk judul banner (opsional)
                TextInput::make('title')
                    ->maxLength(255)
                    ->nullable()
                    ->placeholder('Contoh: Selamat Datang di Website Kami'),

                // Input untuk sub-judul/deskripsi singkat banner (opsional)
                Textarea::make('subtitle')
                    ->rows(2)
                    ->maxLength(65535)
                    ->nullable()
                    ->placeholder('Contoh: Kami adalah perusahaan konstruksi terkemuka...'),

                // Input untuk teks tombol call-to-action (opsional)
                TextInput::make('button_text')
                    ->maxLength(255)
                    ->nullable()
                    ->placeholder('Contoh: Lihat Proyek Kami'),

                // Input untuk tautan tombol (harus URL, opsional)
                TextInput::make('button_link')
                    ->url()
                    ->maxLength(255)
                    ->nullable()
                    ->placeholder('Contoh: /proyek-kami'),

                // Upload gambar banner (wajib)
                FileUpload::make('image')
                    ->label('Gambar Banner')
                    ->image() // Hanya menerima file gambar
                    ->required() // Wajib diisi
                    ->directory('banners') // Disimpan di storage/app/public/banners
                    ->disk('public') // Menggunakan disk 'public' agar bisa diakses via URL
                    ->imageEditor() // Memungkinkan pengeditan gambar setelah diupload
                    ->columnSpanFull(), // Mengambil lebar penuh kolom formulir

                // Toggle untuk mengaktifkan/menonaktifkan banner
                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true), // Defaultnya aktif
            ]);
    }

    // Definisi kolom tabel untuk menampilkan daftar banner
    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                // Kolom untuk menampilkan gambar banner sebagai thumbnail
                ImageColumn::make('image')
                    ->label('Gambar')
                    ->square(), // Membuat gambar tampil kotak

                // Kolom untuk judul banner
                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable() // Bisa dicari
                    ->sortable(), // Bisa diurutkan

                // Kolom untuk sub-judul banner
                TextColumn::make('subtitle')
                    ->label('Sub-judul')
                    ->wrap(), // Membungkus teks jika terlalu panjang

                // Kolom untuk teks tombol
                TextColumn::make('button_text')
                    ->label('Teks Tombol'),

                // Kolom untuk status aktif/tidak aktif (ikon boolean)
                IconColumn::make('is_active')
                    ->label('Status Aktif')
                    ->boolean(), // Menampilkan ikon centang/silang
            ])
            ->filters([
                // Anda bisa menambahkan filter di sini, contoh:
                // Tables\Filters\TernaryFilter::make('is_active')
                //     ->label('Aktif')
                //     ->trueLabel('Aktif')
                //     ->falseLabel('Tidak Aktif')
                //     ->nullable(),
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
            'index' => Pages\ListBanners::route('/'), // Halaman daftar banner
            'create' => Pages\CreateBanner::route('/create'), // Halaman tambah banner
            'edit' => Pages\EditBanner::route('/{record}/edit'), // Halaman edit banner
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