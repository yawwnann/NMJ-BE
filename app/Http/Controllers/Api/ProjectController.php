<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Services\CloudinaryImageService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    protected $cloudinaryService;

    public function __construct(CloudinaryImageService $cloudinaryService)
    {
        $this->cloudinaryService = $cloudinaryService;
    }

    /**
     * Display a listing of projects
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Project::active();

            // Filter by status
            if ($request->has('status')) {
                $query->byStatus($request->status);
            }

            // Filter by construction_category
            if ($request->has('construction_category')) {
                $query->where('construction_category', $request->construction_category);
            }

            $projects = $query->get()->map(function ($project) {
                $start = $project->start_date ? \Carbon\Carbon::parse($project->start_date) : null;
                $end = ($project->is_ongoing || !$project->end_date) ? null : \Carbon\Carbon::parse($project->end_date);
                $durasi = null;
                if ($start && $end) {
                    $days = $start->diffInDays($end) + 1;
                    $months = floor($days / 30);
                    $durasi = $months > 0 ? $months . ' bulan ' . ($days % 30) . ' hari' : $days . ' hari';
                } elseif ($start && $project->is_ongoing) {
                    $days = $start->diffInDays(now()) + 1;
                    $months = floor($days / 30);
                    $durasi = $months > 0 ? $months . ' bulan ' . ($days % 30) . ' hari' : $days . ' hari';
                }
                return [
                    'id' => $project->id,
                    'title' => $project->title,
                    'location' => $project->location,
                    'description' => $project->description,
                    'construction_category' => $project->construction_category,
                    'start_date' => $project->start_date,
                    'end_date' => $project->end_date,
                    'is_ongoing' => $project->is_ongoing,
                    'status' => $project->status,
                    'is_active' => $project->is_active,
                    'image_url' => $project->image_url,
                    'duration' => $durasi,
                    'created_at' => $project->created_at,
                    'updated_at' => $project->updated_at,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $projects,
                'message' => 'Projects retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve projects',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created project
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'location' => 'required|string|max:255',
                'description' => 'required|string',
                'construction_category' => 'required|string|max:255',
                'start_date' => 'required|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'is_ongoing' => 'boolean',
                'status' => 'required|in:planning,in_progress,completed,on_hold,cancelled',
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

            $data = $request->only(['title', 'location', 'description', 'construction_category', 'start_date', 'end_date', 'status', 'is_active']);
            $data['is_ongoing'] = $request->boolean('is_ongoing', false);
            $data['is_active'] = $request->boolean('is_active', true);
            if ($data['is_ongoing']) {
                $data['end_date'] = null;
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

            $project = Project::create($data);

            return response()->json([
                'success' => true,
                'data' => $project,
                'message' => 'Project created successfully'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create project',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified project
     */
    public function show(Project $project): JsonResponse
    {
        try {
            $start = $project->start_date ? \Carbon\Carbon::parse($project->start_date) : null;
            $end = ($project->is_ongoing || !$project->end_date) ? null : \Carbon\Carbon::parse($project->end_date);
            $durasi = null;
            if ($start && $end) {
                $days = $start->diffInDays($end) + 1;
                $months = floor($days / 30);
                $durasi = $months > 0 ? $months . ' bulan ' . ($days % 30) . ' hari' : $days . ' hari';
            } elseif ($start && $project->is_ongoing) {
                $days = $start->diffInDays(now()) + 1;
                $months = floor($days / 30);
                $durasi = $months > 0 ? $months . ' bulan ' . ($days % 30) . ' hari' : $days . ' hari';
            }
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $project->id,
                    'title' => $project->title,
                    'location' => $project->location,
                    'description' => $project->description,
                    'construction_category' => $project->construction_category,
                    'start_date' => $project->start_date,
                    'end_date' => $project->end_date,
                    'is_ongoing' => $project->is_ongoing,
                    'status' => $project->status,
                    'is_active' => $project->is_active,
                    'image_url' => $project->image_url,
                    'duration' => $durasi,
                    'created_at' => $project->created_at,
                    'updated_at' => $project->updated_at,
                ],
                'message' => 'Project retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve project',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified project
     */
    public function update(Request $request, Project $project): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'sometimes|required|string|max:255',
                'location' => 'sometimes|required|string|max:255',
                'description' => 'sometimes|required|string',
                'construction_category' => 'sometimes|required|string|max:255',
                'start_date' => 'sometimes|required|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'is_ongoing' => 'boolean',
                'status' => 'sometimes|required|in:planning,in_progress,completed,on_hold,cancelled',
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

            $data = $request->only(['title', 'location', 'description', 'construction_category', 'start_date', 'end_date', 'status', 'is_active']);
            $data['is_ongoing'] = $request->boolean('is_ongoing', $project->is_ongoing);
            if (isset($data['is_active'])) {
                $data['is_active'] = $request->boolean('is_active');
            }
            if ($data['is_ongoing']) {
                $data['end_date'] = null;
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

            $project->update($data);

            return response()->json([
                'success' => true,
                'data' => $project,
                'message' => 'Project updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update project',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified project
     */
    public function destroy(Project $project): JsonResponse
    {
        try {
            $project->delete();

            return response()->json([
                'success' => true,
                'message' => 'Project deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete project',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get project statistics
     */
    public function statistics(): JsonResponse
    {
        try {
            $stats = [
                'total' => Project::active()->count(),
                'planning' => Project::active()->byStatus('planning')->count(),
                'in_progress' => Project::active()->byStatus('in_progress')->count(),
                'completed' => Project::active()->byStatus('completed')->count(),
                'on_hold' => Project::active()->byStatus('on_hold')->count(),
                'cancelled' => Project::active()->byStatus('cancelled')->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Project statistics retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve project statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}