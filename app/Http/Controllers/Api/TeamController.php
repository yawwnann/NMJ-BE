<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Services\CloudflareImageService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class TeamController extends Controller
{
    protected $cloudflareService;

    public function __construct(CloudflareImageService $cloudflareService)
    {
        $this->cloudflareService = $cloudflareService;
    }

    /**
     * Display a listing of team members
     */
    public function index(): JsonResponse
    {
        try {
            $teams = Team::active()->get();

            return response()->json([
                'success' => true,
                'data' => $teams,
                'message' => 'Team members retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve team members',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created team member
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'position' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
                'email' => 'required|email|max:255',
                'address' => 'required|string',
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

            $data = $request->only(['name', 'position', 'phone', 'email', 'address', 'is_active']);
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

            $team = Team::create($data);

            return response()->json([
                'success' => true,
                'data' => $team,
                'message' => 'Team member created successfully'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create team member',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified team member
     */
    public function show(Team $team): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $team,
                'message' => 'Team member retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve team member',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified team member
     */
    public function update(Request $request, Team $team): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'position' => 'sometimes|required|string|max:255',
                'phone' => 'sometimes|required|string|max:20',
                'email' => 'sometimes|required|email|max:255',
                'address' => 'sometimes|required|string',
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

            $data = $request->only(['name', 'position', 'phone', 'email', 'address', 'is_active']);

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
                if ($team->cloudflare_image_id) {
                    $this->cloudflareService->deleteImage($team->cloudflare_image_id);
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

            $team->update($data);

            return response()->json([
                'success' => true,
                'data' => $team,
                'message' => 'Team member updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update team member',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified team member
     */
    public function destroy(Team $team): JsonResponse
    {
        try {
            // Delete image from Cloudflare if exists
            if ($team->cloudflare_image_id) {
                $this->cloudflareService->deleteImage($team->cloudflare_image_id);
            }

            $team->delete();

            return response()->json([
                'success' => true,
                'message' => 'Team member deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete team member',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}