<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsResource\Pages;
use App\Filament\Resources\NewsResource\RelationManagers;
use App\Models\News;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
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

class NewsResource extends Resource
{
    protected static ?string $model = News::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Berita & Artikel';
    protected static ?string $pluralModelLabel = 'Berita & Artikel';
    protected static ?string $modelLabel = 'Berita';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->label('Judul Berita')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn(string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', \Illuminate\Support\Str::slug($state)) : null),

                TextInput::make('slug')
                    ->label('Slug (URL)')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->helperText('Slug otomatis dibuat dari judul, tapi bisa diubah untuk SEO.'),

                // Thumbnail Berita (Implementasi Filestack)
                FileUpload::make('thumbnail')
                    ->label('Gambar Thumbnail')
                    ->image()
                    ->nullable() // Tetap nullable jika thumbnail tidak wajib
                    ->imageEditor()
                    ->columnSpanFull()
                    ->saveUploadedFileUsing(function (Forms\Components\FileUpload $component, TemporaryUploadedFile $file): ?string {
                        Log::info('Livewire temp file details for Filestack (News Thumbnail). Path: ' . $file->getRealPath() . ' | Size: ' . $file->getSize() . ' | Original Name: ' . $file->getClientOriginalName());

                        if (!$file->exists()) {
                            Log::error('Temporary file for News Thumbnail upload does not exist: ' . $file->getRealPath());
                            // Jika nullable, Anda bisa return null di sini, atau throw Exception jika ingin wajib.
                            return null;
                        }

                        try {
                            $filestackApiKey = config('filestack.api_key');

                            if (empty($filestackApiKey)) {
                                Log::error('Filestack API Key is missing in config/filestack.php or .env for News Thumbnail. Please set FILESTACK_API_KEY.');
                                throw new \Exception('Filestack API Key tidak ditemukan. Pastikan sudah diatur di .env dan config/filestack.php.');
                            }

                            $client = new Client([
                                'base_uri' => 'https://www.filestackapi.com/',
                                'timeout' => 30.0, // Timeout dalam detik
                            ]);

                            // Log sebelum unggah ke Filestack
                            Log::info('Attempting Filestack upload for News Thumbnail file: ' . $file->getClientOriginalName() . ' with size: ' . $file->getSize() . ' bytes.');

                            // Lakukan request POST ke Filestack upload API
                            $response = $client->request('POST', 'api/store/S3', [
                                'query' => [
                                    'key' => $filestackApiKey,
                                    // Anda bisa menambahkan parameter lain seperti 'path' untuk folder khusus berita
                                    // 'path' => '/company-profile/news/thumbnails/' . date('Y-m-d') . '/',
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
                            Log::info('Filestack Upload Response Status (News Thumbnail): ' . $statusCode);
                            Log::info('Filestack Upload Response Body (News Thumbnail): ' . json_encode($body));

                            // Periksa apakah unggahan berhasil dan mendapatkan URL
                            if ($statusCode === 200 && isset($body['url'])) {
                                Log::info('Filestack upload successful for News Thumbnail. URL: ' . $body['url'] . ' | Handle: ' . ($body['handle'] ?? 'N/A'));
                                return $body['url']; // Kembalikan URL Filestack untuk disimpan di database
                            } else {
                                Log::error('Filestack upload failed or returned invalid response for News Thumbnail. Status: ' . $statusCode . ' | Body: ' . json_encode($body));
                                throw new \Exception('Unggahan thumbnail ke Filestack gagal atau respons tidak valid.');
                            }

                        } catch (RequestException $e) {
                            // Tangani exception spesifik dari Guzzle HTTP (misalnya, koneksi ditolak, timeout)
                            $errorMessage = $e->getMessage();
                            if ($e->hasResponse()) {
                                $errorMessage .= ' | Filestack API Response: ' . $e->getResponse()->getBody()->getContents();
                            }
                            Log::error('Filestack Guzzle Request Error (News Thumbnail): ' . $errorMessage . ' | File: ' . $file->getClientOriginalName());
                            // Karena thumbnail nullable, kita bisa return null jika terjadi error fatal
                            return null;
                            // Atau throw new \Exception('Gagal mengunggah thumbnail ke Filestack (Kesalahan Jaringan/API): ' . $errorMessage);
                        } catch (\Exception $e) {
                            // Tangani semua kesalahan umum lainnya
                            Log::error('General Filestack Upload Error (News Thumbnail): ' . $e->getMessage() . ' | File: ' . $file->getClientOriginalName() . ' | Path: ' . $file->getRealPath());
                            // Karena thumbnail nullable, kita bisa return null jika terjadi error fatal
                            return null;
                            // Atau throw new \Exception('Gagal mengunggah thumbnail ke Filestack: ' . $e->getMessage());
                        }
                    }),

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

                TextInput::make('author')
                    ->label('Penulis')
                    ->maxLength(255)
                    ->default('Admin')
                    ->nullable(),
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
                TextColumn::make('author')
                    ->label('Penulis')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Tanggal Publikasi')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListNews::route('/'),
            'create' => Pages\CreateNews::route('/create'),
            'edit' => Pages\EditNews::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [];
    }
}