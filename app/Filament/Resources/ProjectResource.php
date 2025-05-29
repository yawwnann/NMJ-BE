<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers;
use App\Models\Project;
use Cloudinary\Cloudinary as CloudinaryCloudinary;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Resources\Resource;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary; // Tambahkan ini
use Illuminate\Support\Facades\Log; // Tambahkan ini untuk debugging

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;
    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $navigationLabel = 'Proyek';
    protected static ?string $pluralModelLabel = 'Proyek';
    protected static ?string $modelLabel = 'Proyek';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->label('Judul Proyek')
                    ->required()
                    ->maxLength(255),

                TextInput::make('location')
                    ->label('Lokasi')
                    ->maxLength(255)
                    ->nullable(),

                TextInput::make('client')
                    ->label('Klien')
                    ->maxLength(255)
                    ->nullable(),

                Textarea::make('short_description')
                    ->label('Deskripsi Singkat')
                    ->rows(3)
                    ->maxLength(65535)
                    ->nullable(),

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

                // Thumbnail Proyek
                FileUpload::make('thumbnail')
                    ->label('Gambar Thumbnail')
                    ->image()
                    ->nullable() // Tidak wajib untuk thumbnail
                    ->imageEditor()
                    ->columnSpanFull()
                    ->saveUploadedFileUsing(function (Forms\Components\FileUpload $component, \Livewire\Features\SupportFileUploads\TemporaryUploadedFile $file): ?string {
                        try {
                            $uploadedFile = Cloudinary::upload($file->getRealPath(), [
                                'folder' => 'company-profile/projects/thumbnails', // Folder di Cloudinary
                                'public_id' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '_' . uniqid(),
                            ]);
                            return $uploadedFile->getSecurePath();
                        } catch (\Exception $e) {
                            Log::error('Cloudinary Thumbnail Upload Error (Project): ' . $e->getMessage());
                            return null; // Return null if upload fails
                        }
                    }),

                // Multiple Images Proyek
                FileUpload::make('images')
                    ->label('Gambar Proyek')
                    ->image()
                    ->multiple()
                    ->nullable()
                    ->imageEditor()
                    ->columnSpanFull()
                    ->saveUploadedFileUsing(function (Forms\Components\FileUpload $component, \Livewire\Features\SupportFileUploads\TemporaryUploadedFile $file): ?string {
                        try {
                            $uploadedFile = Cloudinary::upload($file->getRealPath(), [
                                'folder' => 'company-profile/projects/images', // Folder di Cloudinary
                                'public_id' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '_' . uniqid(),
                            ]);
                            return $uploadedFile->getSecurePath();
                        } catch (\Exception $e) {
                            Log::error('Cloudinary Images Upload Error (Project): ' . $e->getMessage());
                            return null; // Return null if upload fails
                        }
                    }),

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

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                ImageColumn::make('thumbnail')
                    ->label('Thumbnail')
                    ->square(),
                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('location')
                    ->label('Lokasi')
                    ->searchable(),
                TextColumn::make('client')
                    ->label('Klien'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'completed' => 'success',
                        'on_progress' => 'warning',
                        'planned' => 'info',
                    }),
            ])
            ->filters([])
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [];
    }
}