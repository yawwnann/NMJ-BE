<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers;
use App\Models\Project; // Penting: Pastikan ini mengarah ke model Project Anda
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Resources\Resource; // Penting: Pastikan ini ada

class ProjectResource extends Resource
{
    // Mengatur model yang akan digunakan oleh resource ini
    protected static ?string $model = Project::class;

    // Mengatur ikon navigasi yang akan muncul di sidebar admin Filament
    protected static ?string $navigationIcon = 'heroicon-o-briefcase'; // Anda bisa ganti ikon ini

    // Mengatur label singular dan plural untuk navigasi (opsional, defaultnya sudah baik)
    protected static ?string $navigationLabel = 'Proyek';
    protected static ?string $pluralModelLabel = 'Proyek';
    protected static ?string $modelLabel = 'Proyek';

    // Definisi skema formulir untuk membuat atau mengedit proyek
    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                // Input untuk judul proyek (wajib)
                TextInput::make('title')
                    ->label('Judul Proyek')
                    ->required()
                    ->maxLength(255),

                // Input untuk lokasi proyek (opsional)
                TextInput::make('location')
                    ->label('Lokasi')
                    ->maxLength(255)
                    ->nullable(),

                // Input untuk nama klien (opsional)
                TextInput::make('client')
                    ->label('Klien')
                    ->maxLength(255)
                    ->nullable(),

                // Textarea untuk deskripsi singkat proyek (opsional)
                Textarea::make('short_description')
                    ->label('Deskripsi Singkat')
                    ->rows(3)
                    ->maxLength(65535)
                    ->nullable(),

                // Rich text editor untuk deskripsi lengkap proyek (opsional)
                RichEditor::make('description')
                    ->label('Deskripsi Lengkap')
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
                    ->columnSpanFull(),

                // Upload thumbnail proyek (opsional)
                FileUpload::make('thumbnail')
                    ->label('Gambar Thumbnail')
                    ->image()
                    ->directory('project-thumbnails') // Disimpan di storage/app/public/project-thumbnails
                    ->disk('public')
                    ->imageEditor()
                    ->columnSpanFull(),

                // Upload multiple images for the project
                FileUpload::make('images')
                    ->label('Gambar Proyek')
                    ->image()
                    ->multiple()
                    ->directory('project-images')
                    ->disk('public')
                    ->imageEditor()
                    ->columnSpanFull(),

                // Select input for project status
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'completed' => 'Selesai',
                        'on_progress' => 'Sedang Berjalan',
                        'planned' => 'Direncakanan',
                    ])
                    ->default('on_progress')
                    ->required(),
            ]);
    }

    // Definisi kolom tabel untuk menampilkan daftar proyek
    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                // Kolom untuk thumbnail proyek
                ImageColumn::make('thumbnail')
                    ->label('Thumbnail')
                    ->square(),

                // Kolom untuk judul proyek
                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable(),

                // Kolom untuk lokasi proyek
                TextColumn::make('location')
                    ->label('Lokasi')
                    ->searchable(),

                // Kolom untuk klien
                TextColumn::make('client')
                    ->label('Klien'),

                // Kolom untuk status proyek dengan badge warna
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'completed' => 'success',
                        'on_progress' => 'warning',
                        'planned' => 'info',
                    }),
            ])
            ->filters([
                // Anda bisa menambahkan filter di sini, contoh:
                // Tables\Filters\SelectFilter::make('status')
                //     ->options([
                //         'completed' => 'Selesai',
                //         'on_progress' => 'Sedang Berjalan',
                //         'planned' => 'Direncakanan',
                //     ]),
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
            'index' => Pages\ListProjects::route('/'), // Halaman daftar proyek
            'create' => Pages\CreateProject::route('/create'), // Halaman tambah proyek
            'edit' => Pages\EditProject::route('/{record}/edit'), // Halaman edit proyek
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