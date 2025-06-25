<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HeroSection;
use App\Services\CloudinaryImageService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class HeroSectionController extends Controller
{
    protected $cloudinaryService;

    public function __construct(CloudinaryImageService $cloudinaryService)
    {
        $this->cloudinaryService = $cloudinaryService;
    }

    /**
     * Display a listing of hero sections
     */
    public function index(): JsonResponse
    {
        try {
            $heroSections = HeroSection::active()->get();

            return response()->json([
                'success' => true,
                'data' => $heroSections,
                'message' => 'Hero sections retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve hero sections',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created hero section
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240', // 10MB max
                'is_active' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $request->only(['title', 'description', 'is_active']);
            $data['is_active'] = $request->boolean('is_active', true);

            // Handle image upload to Cloudinary
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $result = $this->cloudinaryService->uploadImage($file);
                if ($result && isset($result['url'])) {
                    $data['image_url'] = $result['url'];
                    $data['cloudinary_public_id'] = $result['public_id'] ?? null;
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to upload image to Cloudinary'
                    ], 500);
                }
            }

            $heroSection = HeroSection::create($data);

            return response()->json([
                'success' => true,
                'data' => $heroSection,
                'message' => 'Hero section created successfully'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create hero section',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified hero section
     */
    public function show(HeroSection $heroSection): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $heroSection,
                'message' => 'Hero section retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve hero section',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified hero section
     */
    public function update(Request $request, HeroSection $heroSection): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'sometimes|required|string|max:255',
                'description' => 'sometimes|required|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
                'is_active' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $request->only(['title', 'description', 'is_active']);

            if ($request->has('is_active')) {
                $data['is_active'] = $request->boolean('is_active');
            }

            // Handle image upload to Cloudinary
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $result = $this->cloudinaryService->uploadImage($file);
                if ($result && isset($result['url'])) {
                    $data['image_url'] = $result['url'];
                    $data['cloudinary_public_id'] = $result['public_id'] ?? null;
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to upload image to Cloudinary'
                    ], 500);
                }
            }

            $heroSection->update($data);

            return response()->json([
                'success' => true,
                'data' => $heroSection,
                'message' => 'Hero section updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update hero section',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified hero section
     */
    public function destroy(HeroSection $heroSection): JsonResponse
    {
        try {
            $heroSection->delete();

            return response()->json([
                'success' => true,
                'message' => 'Hero section deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete hero section',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}