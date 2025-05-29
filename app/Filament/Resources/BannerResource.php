<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BannerResource\Pages;
use App\Filament\Resources\BannerResource\RelationManagers;
use App\Models\Banner;
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
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary; // Tambahkan ini
use Illuminate\Support\Facades\Log; // Tambahkan ini untuk debugging

class BannerResource extends Resource
{
    protected static ?string $model = Banner::class;
    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static ?string $navigationLabel = 'Banners';
    protected static ?string $pluralModelLabel = 'Banners';
    protected static ?string $modelLabel = 'Banner';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->maxLength(255)
                    ->nullable()
                    ->placeholder('Contoh: Selamat Datang di Website Kami'),

                Textarea::make('subtitle')
                    ->rows(2)
                    ->maxLength(65535)
                    ->nullable()
                    ->placeholder('Contoh: Kami adalah perusahaan konstruksi terkemuka...'),

                TextInput::make('button_text')
                    ->maxLength(255)
                    ->nullable()
                    ->placeholder('Contoh: Lihat Proyek Kami'),

                TextInput::make('button_link')
                    ->url()
                    ->maxLength(255)
                    ->nullable()
                    ->placeholder('Contoh: /proyek-kami'),

                // Gambar Banner
                FileUpload::make('image')
                    ->label('Gambar Banner')
                    ->image()
                    ->required()
                    ->imageEditor()
                    ->columnSpanFull()
                    ->saveUploadedFileUsing(function (Forms\Components\FileUpload $component, \Livewire\Features\SupportFileUploads\TemporaryUploadedFile $file): string {
                        try {
                            $uploadedFile = Cloudinary::upload($file->getRealPath(), [
                                'folder' => 'company-profile/banners', // Folder di Cloudinary
                                'public_id' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '_' . uniqid(),
                            ]);
                            return $uploadedFile->getSecurePath();
                        } catch (\Exception $e) {
                            Log::error('Cloudinary Image Upload Error (Banner): ' . $e->getMessage());
                            throw new \Exception('Failed to upload image to Cloudinary: ' . $e->getMessage());
                        }
                    }),

                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Gambar')
                    ->square(),
                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable(),
                TextColumn::make('subtitle')
                    ->label('Sub-judul')
                    ->wrap(),
                TextColumn::make('button_text')
                    ->label('Teks Tombol'),
                IconColumn::make('is_active')
                    ->label('Status Aktif')
                    ->boolean(),
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
            'index' => Pages\ListBanners::route('/'),
            'create' => Pages\CreateBanner::route('/create'),
            'edit' => Pages\EditBanner::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [];
    }
}