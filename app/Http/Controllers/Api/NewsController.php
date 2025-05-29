<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    /**
     * Display a listing of the news articles.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $news = News::orderBy('created_at', 'desc')->get(); // Ambil semua berita, urutkan terbaru
        return response()->json([
            'message' => 'News articles retrieved successfully',
            'data' => $news
        ]);
    }

    /**
     * Display the specified news article by slug.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($slug)
    {
        $newsArticle = News::where('slug', $slug)->first(); // Cari berita berdasarkan slug

        if (!$newsArticle) {
            return response()->json([
                'message' => 'News article not found'
            ], 404);
        }

        return response()->json([
            'message' => 'News article retrieved successfully',
            'data' => $newsArticle
        ]);
    }
}