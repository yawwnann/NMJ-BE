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
// Hapus baris ini: use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\Log; // Tetap ada untuk debugging

// Tambahkan library HTTP client seperti Guzzle untuk melakukan request ke Filestack API
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException; // Untuk menangani exception dari Guzzle
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile; // Tambahkan ini jika belum ada secara eksplisit

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
                    // Ganti saveUploadedFileUsing dari Cloudinary ke Filestack
                    ->saveUploadedFileUsing(function (Forms\Components\FileUpload $component, TemporaryUploadedFile $file): ?string {
                        Log::info('Livewire temp file details for Filestack (Banner). Path: ' . $file->getRealPath() . ' | Size: ' . $file->getSize() . ' | Original Name: ' . $file->getClientOriginalName());

                        if (!$file->exists()) {
                            Log::error('Temporary file for Banner upload does not exist: ' . $file->getRealPath());
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
                            Log::info('Attempting Filestack upload for Banner file: ' . $file->getClientOriginalName() . ' with size: ' . $file->getSize() . ' bytes.');

                            // Lakukan request POST ke Filestack upload API
                            $response = $client->request('POST', 'api/store/S3', [
                                'query' => [
                                    'key' => $filestackApiKey,
                                    // Anda bisa menambahkan parameter lain seperti 'path', 'filename', dll.
                                    // Misalnya untuk folder: 'path' => '/company-profile/banners/' . date('Y-m-d') . '/',
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
                            Log::info('Filestack Upload Response Status (Banner): ' . $statusCode);
                            Log::info('Filestack Upload Response Body (Banner): ' . json_encode($body));

                            // Periksa apakah unggahan berhasil dan mendapatkan URL
                            if ($statusCode === 200 && isset($body['url'])) {
                                Log::info('Filestack upload successful for Banner. URL: ' . $body['url'] . ' | Handle: ' . ($body['handle'] ?? 'N/A'));
                                return $body['url']; // Kembalikan URL Filestack untuk disimpan di database
                            } else {
                                Log::error('Filestack upload failed or returned invalid response for Banner. Status: ' . $statusCode . ' | Body: ' . json_encode($body));
                                throw new \Exception('Unggahan ke Filestack gagal atau respons tidak valid.');
                            }

                        } catch (RequestException $e) {
                            // Tangani exception spesifik dari Guzzle HTTP (misalnya, koneksi ditolak, timeout)
                            $errorMessage = $e->getMessage();
                            if ($e->hasResponse()) {
                                $errorMessage .= ' | Filestack API Response: ' . $e->getResponse()->getBody()->getContents();
                            }
                            Log::error('Filestack Guzzle Request Error (Banner): ' . $errorMessage . ' | File: ' . $file->getClientOriginalName());
                            throw new \Exception('Gagal mengunggah gambar ke Filestack (Kesalahan Jaringan/API): ' . $errorMessage);
                        } catch (\Exception $e) {
                            // Tangani semua kesalahan umum lainnya
                            Log::error('General Filestack Upload Error (Banner): ' . $e->getMessage() . ' | File: ' . $file->getClientOriginalName() . ' | Path: ' . $file->getRealPath());
                            throw new \Exception('Gagal mengunggah gambar ke Filestack: ' . $e->getMessage());
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