<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsResource\Pages;
use App\Filament\Resources\NewsResource\RelationManagers;
use App\Models\News; // Penting: Pastikan ini mengarah ke model News Anda
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Resources\Resource; // Penting: Pastikan ini ada

class NewsResource extends Resource
{
    // Mengatur model yang akan digunakan oleh resource ini
    protected static ?string $model = News::class;

    // Mengatur ikon navigasi yang akan muncul di sidebar admin Filament
    protected static ?string $navigationIcon = 'heroicon-o-document-text'; // Anda bisa ganti ikon ini

    // Mengatur label singular dan plural untuk navigasi (opsional, defaultnya sudah baik)
    protected static ?string $navigationLabel = 'Berita & Artikel';
    protected static ?string $pluralModelLabel = 'Berita & Artikel';
    protected static ?string $modelLabel = 'Berita';


    // Definisi skema formulir untuk membuat atau mengedit berita
    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                // Input untuk judul berita (wajib)
                TextInput::make('title')
                    ->label('Judul Berita')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true) // Update slug secara real-time saat judul berubah
                    ->afterStateUpdated(fn(string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', \Illuminate\Support\Str::slug($state)) : null),

                // Input untuk slug (URL friendly string, wajib dan unik)
                TextInput::make('slug')
                    ->label('Slug (URL)')
                    ->required()
                    ->unique(ignoreRecord: true) // Slug harus unik, kecuali untuk record yang sedang diedit
                    ->maxLength(255)
                    ->helperText('Slug otomatis dibuat dari judul, tapi bisa diubah untuk SEO.'),

                // Upload thumbnail berita (opsional)
                FileUpload::make('thumbnail')
                    ->label('Gambar Thumbnail')
                    ->image()
                    ->directory('news-thumbnails') // Disimpan di storage/app/public/news-thumbnails
                    ->disk('public')
                    ->imageEditor()
                    ->columnSpanFull(), // Mengambil lebar penuh kolom formulir

                // Rich text editor untuk konten berita (wajib)
                RichEditor::make('content')
                    ->label('Konten Berita')
                    ->toolbarButtons([
                        'blockquote',
                        'bold',
                        'bulletList',
                        'codeBlock',
                        'h2',
                        'h3',
                        'italic',
                        'link',
                        'orderedList',
                        'redo',
                        'strike',
                        'underline',
                        'undo',
                    ])
                    ->required()
                    ->columnSpanFull(),

                // Input untuk nama penulis (opsional, default 'Admin')
                TextInput::make('author')
                    ->label('Penulis')
                    ->maxLength(255)
                    ->default('Admin')
                    ->nullable(),
            ]);
    }

    // Definisi kolom tabel untuk menampilkan daftar berita
    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                // Kolom untuk menampilkan thumbnail berita
                ImageColumn::make('thumbnail')
                    ->label('Thumbnail')
                    ->square(),

                // Kolom untuk judul berita
                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable(),

                // Kolom untuk penulis
                TextColumn::make('author')
                    ->label('Penulis')
                    ->searchable(),

                // Kolom untuk tanggal pembuatan berita (tersembunyi secara default, bisa di-toggle)
                TextColumn::make('created_at')
                    ->label('Tanggal Publikasi')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true), // Sembunyikan secara default
            ])
            ->filters([
                // Anda bisa menambahkan filter di sini jika diperlukan, contoh:
                // Tables\Filters\SelectFilter::make('author')
                //     ->options(News::distinct()->pluck('author', 'author')->toArray()),
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
            'index' => Pages\ListNews::route('/'), // Halaman daftar berita
            'create' => Pages\CreateNews::route('/create'), // Halaman tambah berita
            'edit' => Pages\EditNews::route('/{record}/edit'), // Halaman edit berita
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