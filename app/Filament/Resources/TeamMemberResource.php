<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeamMemberResource\Pages;
use App\Filament\Resources\TeamMemberResource\RelationManagers;
use App\Models\TeamMember; // Penting: Pastikan ini mengarah ke model TeamMember Anda
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Resources\Resource; // Penting: Pastikan ini ada

class TeamMemberResource extends Resource
{
    // Mengatur model yang akan digunakan oleh resource ini
    protected static ?string $model = TeamMember::class;

    // Mengatur ikon navigasi yang akan muncul di sidebar admin Filament
    protected static ?string $navigationIcon = 'heroicon-o-users'; // Anda bisa ganti ikon ini

    // Mengatur label singular dan plural untuk navigasi (opsional, defaultnya sudah baik)
    protected static ?string $navigationLabel = 'Tim Pengurus';
    protected static ?string $pluralModelLabel = 'Tim Pengurus';
    protected static ?string $modelLabel = 'Anggota Tim';

    // Definisi skema formulir untuk membuat atau mengedit anggota tim
    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                // Input untuk nama anggota tim (wajib)
                TextInput::make('name')
                    ->label('Nama Lengkap')
                    ->required()
                    ->maxLength(255),

                // Input untuk posisi/jabatan (wajib)
                TextInput::make('position')
                    ->label('Posisi/Jabatan')
                    ->required()
                    ->maxLength(255),

                // Input untuk email (opsional)
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->maxLength(255)
                    ->nullable(),

                // Input untuk nomor telepon (opsional)
                TextInput::make('phone')
                    ->label('Nomor Telepon')
                    ->tel() // Validasi untuk format telepon
                    ->maxLength(255)
                    ->nullable(),

                // Upload foto profil anggota tim (opsional)
                FileUpload::make('photo')
                    ->label('Foto Profil')
                    ->image() // Hanya menerima file gambar
                    ->directory('team-photos') // Disimpan di storage/app/public/team-photos
                    ->disk('public') // Menggunakan disk 'public' agar bisa diakses via URL
                    ->imageEditor() // Memungkinkan pengeditan gambar setelah diupload
                    ->columnSpanFull(), // Mengambil lebar penuh kolom formulir
            ]);
    }

    // Definisi kolom tabel untuk menampilkan daftar anggota tim
    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                // Kolom untuk foto profil
                ImageColumn::make('photo')
                    ->label('Foto')
                    ->square(), // Membuat gambar tampil kotak

                // Kolom untuk nama anggota tim
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                // Kolom untuk posisi
                TextColumn::make('position')
                    ->label('Posisi')
                    ->searchable(),

                // Kolom untuk email
                TextColumn::make('email')
                    ->label('Email'),

                // Kolom untuk nomor telepon
                TextColumn::make('phone')
                    ->label('Telepon'),
            ])
            ->filters([
                // Anda bisa menambahkan filter di sini jika diperlukan, contoh:
                // Tables\Filters\SelectFilter::make('position')
                //     ->options(TeamMember::distinct()->pluck('position', 'position')->toArray()),
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
            'index' => Pages\ListTeamMembers::route('/'), // Halaman daftar anggota tim
            'create' => Pages\CreateTeamMember::route('/create'), // Halaman tambah anggota tim
            'edit' => Pages\EditTeamMember::route('/{record}/edit'), // Halaman edit anggota tim
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