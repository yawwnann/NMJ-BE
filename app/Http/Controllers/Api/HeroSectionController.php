<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HeroSection;
use App\Services\CloudflareImageService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class HeroSectionController extends Controller
{
    protected $cloudflareService;

    public function __construct(CloudflareImageService $cloudflareService)
    {
        $this->cloudflareService = $cloudflareService;
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

            // Handle image upload to Cloudflare
            if ($request->hasFile('image')) {
                $file = $request->file('image');

                if (!$this->cloudflareService->validateFile($file)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid image file'
                    ], 422);
                }

                $uploadResult = $this->cloudflareService->uploadImage($file);

                if ($uploadResult) {
                    $data['image_url'] = $uploadResult['url'];
                    $data['cloudflare_image_id'] = $uploadResult['id'];
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to upload image to Cloudflare'
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

            // Handle image upload to Cloudflare
            if ($request->hasFile('image')) {
                $file = $request->file('image');

                if (!$this->cloudflareService->validateFile($file)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid image file'
                    ], 422);
                }

                // Delete old image from Cloudflare if exists
                if ($heroSection->cloudflare_image_id) {
                    $this->cloudflareService->deleteImage($heroSection->cloudflare_image_id);
                }

                $uploadResult = $this->cloudflareService->uploadImage($file);

                if ($uploadResult) {
                    $data['image_url'] = $uploadResult['url'];
                    $data['cloudflare_image_id'] = $uploadResult['id'];
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to upload image to Cloudflare'
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
            // Delete image from Cloudflare if exists
            if ($heroSection->cloudflare_image_id) {
                $this->cloudflareService->deleteImage($heroSection->cloudflare_image_id);
            }

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