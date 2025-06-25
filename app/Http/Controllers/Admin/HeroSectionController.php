<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HeroSection;
use Illuminate\Support\Str;
use App\Services\CloudinaryNativeService;

class HeroSectionController extends Controller
{
    public function index()
    {
        $heros = HeroSection::all();
        return view('admin.hero.index', compact('heros'));
    }

    public function create()
    {
        return view('admin.hero.create');
    }

    public function store(Request $request)
    {
        //     dd([
        //     'CLOUDINARY_CLOUD_NAME' => env('CLOUDINARY_CLOUD_NAME'),
        //     'CLOUDINARY_API_KEY' => env('CLOUDINARY_API_KEY'),
        //     'CLOUDINARY_API_SECRET' => env('CLOUDINARY_API_SECRET'),
        //     'config_cloud_name' => config('cloudinary.cloud.cloud_name'),
        //     'config_api_key' => config('cloudinary.cloud.api_key'),
        //     'config_api_secret' => config('cloudinary.cloud.api_secret'),
        // ]);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'nullable|boolean',
        ]);
        $validated['is_active'] = $request->has('is_active');

        $imageUrl = null;
        if ($request->hasFile('image')) {
            $cloudinary = new CloudinaryNativeService();
            $result = $cloudinary->uploadImage($request->file('image'));
            if ($result && isset($result['url'])) {
                $imageUrl = $result['url'];
            } else {
                return back()->withErrors(['image' => 'Gagal upload gambar, cek konfigurasi Cloudinary.'])->withInput();
            }
        }
        $validated['image_url'] = $imageUrl;
        HeroSection::create($validated);
        return redirect()->route('admin.hero.index')->with('success', 'Hero section berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $hero = HeroSection::findOrFail($id);
        return view('admin.hero.edit', compact('hero'));
    }

    public function update(Request $request, $id)
    {
        $hero = HeroSection::findOrFail($id);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'nullable|boolean',
        ]);
        $validated['is_active'] = $request->has('is_active');

        $imageUrl = $hero->image_url;
        if ($request->hasFile('image')) {
            $cloudinary = new CloudinaryNativeService();
            $result = $cloudinary->uploadImage($request->file('image'));
            if ($result && isset($result['url'])) {
                $imageUrl = $result['url'];
            } else {
                return back()->withErrors(['image' => 'Gagal upload gambar, cek konfigurasi Cloudinary.'])->withInput();
            }
        }
        $validated['image_url'] = $imageUrl;
        $hero->update($validated);
        return redirect()->route('admin.hero.index')->with('success', 'Hero section berhasil diupdate.');
    }

    public function destroy($id)
    {
        $hero = HeroSection::findOrFail($id);
        $hero->delete();
        return redirect()->route('admin.hero.index')->with('success', 'Hero section berhasil dihapus.');
    }
}