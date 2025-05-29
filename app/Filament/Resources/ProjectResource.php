<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers;
use App\Models\Project;
// Hapus baris ini: use Cloudinary\Cloudinary as CloudinaryCloudinary; (ini duplikat dari CloudinaryLabs)
// Hapus baris ini: use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
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
use Illuminate\Support\Facades\Log; // Tetap ada untuk debugging

// Tambahkan library HTTP client seperti Guzzle untuk melakukan request ke Filestack API
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException; // Untuk menangani exception dari Guzzle
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile; // Tambahkan ini jika belum ada secara eksplisit

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

                // Thumbnail Proyek (Implementasi Filestack)
                FileUpload::make('thumbnail')
                    ->label('Gambar Thumbnail')
                    ->image()
                    ->nullable() // Tidak wajib untuk thumbnail
                    ->imageEditor()
                    ->columnSpanFull()
                    ->saveUploadedFileUsing(function (Forms\Components\FileUpload $component, TemporaryUploadedFile $file): ?string {
                        Log::info('Livewire temp file details for Filestack (Project Thumbnail). Path: ' . $file->getRealPath() . ' | Size: ' . $file->getSize() . ' | Original Name: ' . $file->getClientOriginalName());

                        if (!$file->exists()) {
                            Log::error('Temporary file for Project Thumbnail upload does not exist: ' . $file->getRealPath());
                            return null;
                        }

                        try {
                            $filestackApiKey = config('filestack.api_key');

                            if (empty($filestackApiKey)) {
                                Log::error('Filestack API Key is missing in config/filestack.php or .env for Project Thumbnail. Please set FILESTACK_API_KEY.');
                                throw new \Exception('Filestack API Key tidak ditemukan. Pastikan sudah diatur di .env dan config/filestack.php.');
                            }

                            $client = new Client([
                                'base_uri' => 'https://www.filestackapi.com/',
                                'timeout' => 30.0, // Timeout dalam detik
                            ]);

                            Log::info('Attempting Filestack upload for Project Thumbnail file: ' . $file->getClientOriginalName() . ' with size: ' . $file->getSize() . ' bytes.');

                            $response = $client->request('POST', 'api/store/S3', [
                                'query' => [
                                    'key' => $filestackApiKey,
                                    // Anda bisa menambahkan parameter 'path' untuk folder khusus proyek
                                    // 'path' => '/company-profile/projects/thumbnails/' . date('Y-m-d') . '/',
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

                            Log::info('Filestack Upload Response Status (Project Thumbnail): ' . $statusCode);
                            Log::info('Filestack Upload Response Body (Project Thumbnail): ' . json_encode($body));

                            if ($statusCode === 200 && isset($body['url'])) {
                                Log::info('Filestack upload successful for Project Thumbnail. URL: ' . $body['url']);
                                return $body['url'];
                            } else {
                                Log::error('Filestack upload failed or returned invalid response for Project Thumbnail. Status: ' . $statusCode . ' | Body: ' . json_encode($body));
                                throw new \Exception('Unggahan thumbnail proyek ke Filestack gagal atau respons tidak valid.');
                            }

                        } catch (RequestException $e) {
                            $errorMessage = $e->getMessage();
                            if ($e->hasResponse()) {
                                $errorMessage .= ' | Filestack API Response: ' . $e->getResponse()->getBody()->getContents();
                            }
                            Log::error('Filestack Guzzle Request Error (Project Thumbnail): ' . $errorMessage . ' | File: ' . $file->getClientOriginalName());
                            return null; // Return null jika terjadi error fatal
                        } catch (\Exception $e) {
                            Log::error('General Filestack Upload Error (Project Thumbnail): ' . $e->getMessage() . ' | File: ' . $file->getClientOriginalName() . ' | Path: ' . $file->getRealPath());
                            return null; // Return null jika terjadi error fatal
                        }
                    }),

                // Multiple Images Proyek (Implementasi Filestack)
                FileUpload::make('images')
                    ->label('Gambar Proyek')
                    ->image()
                    ->multiple()
                    ->nullable()
                    ->imageEditor()
                    ->columnSpanFull()
                    ->saveUploadedFileUsing(function (Forms\Components\FileUpload $component, TemporaryUploadedFile $file): ?string {
                        Log::info('Livewire temp file details for Filestack (Project Images). Path: ' . $file->getRealPath() . ' | Size: ' . $file->getSize() . ' | Original Name: ' . $file->getClientOriginalName());

                        if (!$file->exists()) {
                            Log::error('Temporary file for Project Images upload does not exist: ' . $file->getRealPath());
                            return null;
                        }

                        try {
                            $filestackApiKey = config('filestack.api_key');

                            if (empty($filestackApiKey)) {
                                Log::error('Filestack API Key is missing in config/filestack.php or .env for Project Images. Please set FILESTACK_API_KEY.');
                                throw new \Exception('Filestack API Key tidak ditemukan. Pastikan sudah diatur di .env dan config/filestack.php.');
                            }

                            $client = new Client([
                                'base_uri' => 'https://www.filestackapi.com/',
                                'timeout' => 30.0, // Timeout dalam detik
                            ]);

                            Log::info('Attempting Filestack upload for Project Images file: ' . $file->getClientOriginalName() . ' with size: ' . $file->getSize() . ' bytes.');

                            $response = $client->request('POST', 'api/store/S3', [
                                'query' => [
                                    'key' => $filestackApiKey,
                                    // Anda bisa menambahkan parameter 'path' untuk folder khusus proyek
                                    // 'path' => '/company-profile/projects/images/' . date('Y-m-d') . '/',
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

                            Log::info('Filestack Upload Response Status (Project Images): ' . $statusCode);
                            Log::info('Filestack Upload Response Body (Project Images): ' . json_encode($body));

                            if ($statusCode === 200 && isset($body['url'])) {
                                Log::info('Filestack upload successful for Project Images. URL: ' . $body['url']);
                                return $body['url'];
                            } else {
                                Log::error('Filestack upload failed or returned invalid response for Project Images. Status: ' . $statusCode . ' | Body: ' . json_encode($body));
                                throw new \Exception('Unggahan gambar proyek ke Filestack gagal atau respons tidak valid.');
                            }

                        } catch (RequestException $e) {
                            $errorMessage = $e->getMessage();
                            if ($e->hasResponse()) {
                                $errorMessage .= ' | Filestack API Response: ' . $e->getResponse()->getBody()->getContents();
                            }
                            Log::error('Filestack Guzzle Request Error (Project Images): ' . $errorMessage . ' | File: ' . $file->getClientOriginalName());
                            return null; // Return null jika terjadi error fatal
                        } catch (\Exception $e) {
                            Log::error('General Filestack Upload Error (Project Images): ' . $e->getMessage() . ' | File: ' . $file->getClientOriginalName() . ' | Path: ' . $file->getRealPath());
                            return null; // Return null jika terjadi error fatal
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