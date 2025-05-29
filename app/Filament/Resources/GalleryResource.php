<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GalleryResource\Pages;
use App\Filament\Resources\GalleryResource\RelationManagers;
use App\Models\Gallery;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Resources\Resource;
// Hapus CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\Log;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Illuminate\Support\Facades\Storage; // Tetap ada jika Anda menggunakannya untuk hal lain

// Tambahkan library HTTP client seperti Guzzle untuk melakukan request ke Filestack API
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException; // Untuk menangani exception dari Guzzle

class GalleryResource extends Resource
{
    protected static ?string $model = Gallery::class;
    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static ?string $navigationLabel = 'Galeri Foto';
    protected static ?string $pluralModelLabel = 'Galeri Foto';
    protected static ?string $modelLabel = 'Foto Galeri';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                TextInput::make('caption')
                    ->label('Keterangan Foto')
                    ->maxLength(255)
                    ->nullable()
                    ->placeholder('Contoh: Pembangunan Gedung A'),

                FileUpload::make('image')
                    ->label('Gambar Galeri')
                    ->image()
                    ->required()
                    ->imageEditor()
                    ->columnSpanFull()
                    ->saveUploadedFileUsing(function (Forms\Components\FileUpload $component, TemporaryUploadedFile $file): ?string {
                        Log::info('Livewire temp file details for Filestack. Path: ' . $file->getRealPath() . ' | Size: ' . $file->getSize() . ' | Original Name: ' . $file->getClientOriginalName());

                        if (!$file->exists()) {
                            Log::error('Temporary file for Gallery upload does not exist: ' . $file->getRealPath());
                            throw new \Exception('File sementara tidak ditemukan untuk diunggah ke Filestack.');
                        }

                        try {
                            $filestackApiKey = config('filestack.api_key');

                            if (empty($filestackApiKey)) {
                                Log::error('Filestack API Key is missing in config/filestack.php or .env. Please set FILESTACK_API_KEY.');
                                throw new \Exception('Filestack API Key tidak ditemukan. Pastikan sudah diatur di .env dan config/filestack.php.');
                            }

                            $client = new Client([
                                'base_uri' => 'https://www.filestackapi.com/',
                                'timeout' => 30.0, // Timeout dalam detik
                            ]);

                            // Log sebelum unggah ke Filestack
                            Log::info('Attempting Filestack upload for Gallery file: ' . $file->getClientOriginalName() . ' with size: ' . $file->getSize() . ' bytes.');

                            // Lakukan request POST ke Filestack upload API
                            $response = $client->request('POST', 'api/store/S3', [
                                'query' => [
                                    'key' => $filestackApiKey,
                                    // Anda bisa menambahkan parameter lain seperti 'path', 'filename', dll.
                                    // 'path' => '/my_app_gallery/' . date('Y-m-d') . '/',
                                    // 'filename' => $file->getClientOriginalName(),
                                ],
                                'multipart' => [
                                    [
                                        'name' => 'fileUpload', // Nama field untuk file
                                        'contents' => fopen($file->getRealPath(), 'r'),
                                        'filename' => $file->getClientOriginalName(),
                                    ],
                                ],
                            ]);

                            $statusCode = $response->getStatusCode();
                            $body = json_decode($response->getBody()->getContents(), true);

                            // Log hasil respons dari Filestack
                            Log::info('Filestack Upload Response Status: ' . $statusCode);
                            Log::info('Filestack Upload Response Body: ' . json_encode($body));

                            // Periksa apakah unggahan berhasil dan mendapatkan URL
                            if ($statusCode === 200 && isset($body['url'])) {
                                Log::info('Filestack upload successful for Gallery. URL: ' . $body['url'] . ' | Handle: ' . ($body['handle'] ?? 'N/A'));
                                return $body['url']; // Kembalikan URL Filestack untuk disimpan di database
                            } else {
                                Log::error('Filestack upload failed or returned invalid response for Gallery. Status: ' . $statusCode . ' | Body: ' . json_encode($body));
                                throw new \Exception('Unggahan ke Filestack gagal atau respons tidak valid.');
                            }

                        } catch (RequestException $e) {
                            // Tangani exception spesifik dari Guzzle HTTP (misalnya, koneksi ditolak, timeout)
                            $errorMessage = $e->getMessage();
                            if ($e->hasResponse()) {
                                $errorMessage .= ' | Filestack API Response: ' . $e->getResponse()->getBody()->getContents();
                            }
                            Log::error('Filestack Guzzle Request Error (Gallery): ' . $errorMessage . ' | File: ' . $file->getClientOriginalName());
                            throw new \Exception('Gagal mengunggah gambar ke Filestack (Kesalahan Jaringan/API): ' . $errorMessage);
                        } catch (\Exception $e) {
                            // Tangani semua kesalahan umum lainnya
                            Log::error('General Filestack Upload Error (Gallery): ' . $e->getMessage() . ' | File: ' . $file->getClientOriginalName() . ' | Path: ' . $file->getRealPath());
                            throw new \Exception('Gagal mengunggah gambar ke Filestack: ' . $e->getMessage());
                        }
                    }),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Gambar')
                    ->square(),
                TextColumn::make('caption')
                    ->label('Keterangan')
                    ->searchable()
                    ->sortable(),
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
            'index' => Pages\ListGalleries::route('/'),
            'create' => Pages\CreateGallery::route('/create'),
            'edit' => Pages\EditGallery::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }
}