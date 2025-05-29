<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeamMemberResource\Pages;
use App\Filament\Resources\TeamMemberResource\RelationManagers;
use App\Models\TeamMember;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Resources\Resource;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary; // Tambahkan ini
use Illuminate\Support\Facades\Log; // Tambahkan ini untuk debugging

class TeamMemberResource extends Resource
{
    protected static ?string $model = TeamMember::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Tim Pengurus';
    protected static ?string $pluralModelLabel = 'Tim Pengurus';
    protected static ?string $modelLabel = 'Anggota Tim';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nama Lengkap')
                    ->required()
                    ->maxLength(255),

                TextInput::make('position')
                    ->label('Posisi/Jabatan')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->maxLength(255)
                    ->nullable(),

                TextInput::make('phone')
                    ->label('Nomor Telepon')
                    ->tel()
                    ->maxLength(255)
                    ->nullable(),

                // Foto Profil Anggota Tim
                FileUpload::make('photo')
                    ->label('Foto Profil')
                    ->image()
                    ->nullable() // Opsional
                    ->imageEditor()
                    ->columnSpanFull()
                    ->saveUploadedFileUsing(function (Forms\Components\FileUpload $component, \Livewire\Features\SupportFileUploads\TemporaryUploadedFile $file): ?string {
                        try {
                            $uploadedFile = Cloudinary::upload($file->getRealPath(), [
                                'folder' => 'company-profile/team-photos', // Folder di Cloudinary
                                'public_id' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '_' . uniqid(),
                            ]);
                            return $uploadedFile->getSecurePath();
                        } catch (\Exception $e) {
                            Log::error('Cloudinary Photo Upload Error (Team Member): ' . $e->getMessage());
                            return null;
                        }
                    }),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo')
                    ->label('Foto')
                    ->square(),
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('position')
                    ->label('Posisi')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email'),
                TextColumn::make('phone')
                    ->label('Telepon'),
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
            'index' => Pages\ListTeamMembers::route('/'),
            'create' => Pages\CreateTeamMember::route('/create'),
            'edit' => Pages\EditTeamMember::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [];
    }
}