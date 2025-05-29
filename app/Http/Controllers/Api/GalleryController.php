<?php

namespace App\Http\Controllers\Api; // <-- PASTIKAN NAMESPACE INI BENAR

use App\Http\Controllers\Controller;
use App\Models\Gallery; // <-- PASTIKAN INI BENAR
use Illuminate\Http\Request;

class GalleryController extends Controller // <-- PASTIKAN NAMA KELAS INI BENAR
{
    /**
     * Display a listing of the gallery images.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index() // <-- PASTIKAN NAMA METHOD INI BENAR
    {
        $galleryImages = Gallery::orderBy('created_at', 'desc')->get();
        return response()->json([
            'message' => 'Gallery images retrieved successfully',
            'data' => $galleryImages
        ]);
    }

    /**
     * Display the specified gallery image.
     *
     * @param  \App\Models\Gallery  $gallery
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Gallery $gallery) // <-- PASTIKAN NAMA METHOD INI BENAR
    {
        return response()->json([
            'message' => 'Gallery image retrieved successfully',
            'data' => $gallery
        ]);
    }
}