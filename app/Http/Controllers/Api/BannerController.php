<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    /**
     * Display a listing of the active banners.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Ambil banner yang aktif, bisa diurutkan berdasarkan keinginan (misal: order)
        $banners = Banner::where('is_active', true)->orderBy('created_at', 'asc')->get();
        return response()->json([
            'message' => 'Active banners retrieved successfully',
            'data' => $banners
        ]);
    }

    /**
     * Display the specified banner.
     *
     * @param  \App\Models\Banner  $banner
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Banner $banner)
    {
        return response()->json([
            'message' => 'Banner retrieved successfully',
            'data' => $banner
        ]);
    }
}