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
// Hapus baris ini: use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\Log; // Tetap ada untuk debugging

// Tambahkan library HTTP client seperti Guzzle untuk melakukan request ke Filestack API
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException; // Untuk menangani exception dari Guzzle
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile; // Tambahkan ini jika belum ada secara eksplisit

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

                // Foto Profil Anggota Tim (Implementasi Filestack)
                FileUpload::make('photo')
                    ->label('Foto Profil')
                    ->image()
                    ->nullable() // Opsional
                    ->imageEditor()
                    ->columnSpanFull()
                    ->saveUploadedFileUsing(function (Forms\Components\FileUpload $component, TemporaryUploadedFile $file): ?string {
                        Log::info('Livewire temp file details for Filestack (Team Member Photo). Path: ' . $file->getRealPath() . ' | Size: ' . $file->getSize() . ' | Original Name: ' . $file->getClientOriginalName());

                        if (!$file->exists()) {
                            Log::error('Temporary file for Team Member Photo upload does not exist: ' . $file->getRealPath());
                            return null;
                        }

                        try {
                            $filestackApiKey = config('filestack.api_key');

                            if (empty($filestackApiKey)) {
                                Log::error('Filestack API Key is missing in config/filestack.php or .env for Team Member Photo. Please set FILESTACK_API_KEY.');
                                throw new \Exception('Filestack API Key tidak ditemukan. Pastikan sudah diatur di .env dan config/filestack.php.');
                            }

                            $client = new Client([
                                'base_uri' => 'https://www.filestackapi.com/',
                                'timeout' => 30.0, // Timeout dalam detik
                            ]);

                            Log::info('Attempting Filestack upload for Team Member Photo file: ' . $file->getClientOriginalName() . ' with size: ' . $file->getSize() . ' bytes.');

                            $response = $client->request('POST', 'api/store/S3', [
                                'query' => [
                                    'key' => $filestackApiKey,
                                    // Anda bisa menambahkan parameter 'path' untuk folder khusus foto tim
                                    // 'path' => '/company-profile/team-photos/' . date('Y-m-d') . '/',
                                ],
                                'multipart' => [
                                    [
                                        'name' => 'fileUpload',
                                        'contents' => fopen($file->getRealPath(), 'r'),
                                        'filename' => $file->getClientOriginalName(),
                                    ],
                                ],
                            ]);

                            $statusCode = $response->getStatusCode();
                            $body = json_decode($response->getBody()->getContents(), true);

                            Log::info('Filestack Upload Response Status (Team Member Photo): ' . $statusCode);
                            Log::info('Filestack Upload Response Body (Team Member Photo): ' . json_encode($body));

                            if ($statusCode === 200 && isset($body['url'])) {
                                Log::info('Filestack upload successful for Team Member Photo. URL: ' . $body['url']);
                                return $body['url'];
                            } else {
                                Log::error('Filestack upload failed or returned invalid response for Team Member Photo. Status: ' . $statusCode . ' | Body: ' . json_encode($body));
                                throw new \Exception('Unggahan foto profil ke Filestack gagal atau respons tidak valid.');
                            }

                        } catch (RequestException $e) {
                            $errorMessage = $e->getMessage();
                            if ($e->hasResponse()) {
                                $errorMessage .= ' | Filestack API Response: ' . $e->getResponse()->getBody()->getContents();
                            }
                            Log::error('Filestack Guzzle Request Error (Team Member Photo): ' . $errorMessage . ' | File: ' . $file->getClientOriginalName());
                            return null; // Return null jika terjadi error fatal
                        } catch (\Exception $e) {
                            Log::error('General Filestack Upload Error (Team Member Photo): ' . $e->getMessage() . ' | File: ' . $file->getClientOriginalName() . ' | Path: ' . $file->getRealPath());
                            return null; // Return null jika terjadi error fatal
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
            ->filters([
                //
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