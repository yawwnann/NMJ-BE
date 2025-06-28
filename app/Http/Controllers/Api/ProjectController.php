<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectImage;
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
            $query = Project::active()->with(['mainImage', 'workImages', 'galleryImages']);

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
                    'main_image' => $project->mainImage ? [
                        'id' => $project->mainImage->id,
                        'url' => $project->mainImage->image_url,
                        'alt_text' => $project->mainImage->alt_text,
                        'caption' => $project->mainImage->caption
                    ] : null,
                    'work_images' => $project->workImages->map(function ($image) {
                        return [
                            'id' => $image->id,
                            'url' => $image->image_url,
                            'alt_text' => $image->alt_text,
                            'caption' => $image->caption,
                            'sort_order' => $image->sort_order
                        ];
                    }),
                    'gallery_images' => $project->galleryImages->map(function ($image) {
                        return [
                            'id' => $image->id,
                            'url' => $image->image_url,
                            'alt_text' => $image->alt_text,
                            'caption' => $image->caption,
                            'sort_order' => $image->sort_order
                        ];
                    }),
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
                'main_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
                'work_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
                'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
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

            $project = Project::create($data);

            // Handle main image
            if ($request->hasFile('main_image')) {
                $this->uploadProjectImage($project, $request->file('main_image'), 'main');
            }

            // Handle work images
            if ($request->hasFile('work_images')) {
                foreach ($request->file('work_images') as $index => $file) {
                    if ($file && $file->isValid()) {
                        $this->uploadProjectImage($project, $file, 'work', $index);
                    }
                }
            }

            // Handle gallery images
            if ($request->hasFile('gallery_images')) {
                foreach ($request->file('gallery_images') as $index => $file) {
                    if ($file && $file->isValid()) {
                        $this->uploadProjectImage($project, $file, 'gallery', $index);
                    }
                }
            }

            // Load the project with images for response
            $project->load(['mainImage', 'workImages', 'galleryImages']);

            return response()->json([
                'success' => true,
                'data' => $this->formatProjectData($project),
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
            $project->load(['mainImage', 'workImages', 'galleryImages']);

            return response()->json([
                'success' => true,
                'data' => $this->formatProjectData($project),
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
                'title' => 'required|string|max:255',
                'location' => 'required|string|max:255',
                'description' => 'required|string',
                'construction_category' => 'required|string|max:255',
                'start_date' => 'required|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'is_ongoing' => 'boolean',
                'status' => 'required|in:planning,in_progress,completed,on_hold,cancelled',
                'main_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
                'work_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
                'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
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

            $project->update($data);

            // Handle main image
            if ($request->hasFile('main_image')) {
                // Delete existing main image
                $project->mainImage?->delete();
                $this->uploadProjectImage($project, $request->file('main_image'), 'main');
            }

            // Handle work images
            if ($request->hasFile('work_images')) {
                foreach ($request->file('work_images') as $index => $file) {
                    if ($file && $file->isValid()) {
                        $this->uploadProjectImage($project, $file, 'work', $index);
                    }
                }
            }

            // Handle gallery images
            if ($request->hasFile('gallery_images')) {
                foreach ($request->file('gallery_images') as $index => $file) {
                    if ($file && $file->isValid()) {
                        $this->uploadProjectImage($project, $file, 'gallery', $index);
                    }
                }
            }

            // Load the project with images for response
            $project->load(['mainImage', 'workImages', 'galleryImages']);

            return response()->json([
                'success' => true,
                'data' => $this->formatProjectData($project),
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
            // Delete all associated images
            $project->images()->delete();
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
            $totalProjects = Project::count();
            $activeProjects = Project::active()->count();
            $completedProjects = Project::byStatus('completed')->count();
            $ongoingProjects = Project::where('is_ongoing', true)->count();

            $categoryStats = Project::selectRaw('construction_category, COUNT(*) as count')
                ->groupBy('construction_category')
                ->get();

            $statusStats = Project::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_projects' => $totalProjects,
                    'active_projects' => $activeProjects,
                    'completed_projects' => $completedProjects,
                    'ongoing_projects' => $ongoingProjects,
                    'category_statistics' => $categoryStats,
                    'status_statistics' => $statusStats
                ],
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

    /**
     * Delete a specific project image
     */
    public function deleteImage(ProjectImage $image): JsonResponse
    {
        try {
            $image->delete();

            return response()->json([
                'success' => true,
                'message' => 'Image deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete image',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload project image helper method
     */
    private function uploadProjectImage($project, $file, $type, $sortOrder = 0)
    {
        $result = $this->cloudinaryService->uploadImage($file);

        if ($result && isset($result['url'])) {
            ProjectImage::create([
                'project_id' => $project->id,
                'image_url' => $result['url'],
                'cloudinary_public_id' => $result['public_id'] ?? null,
                'type' => $type,
                'sort_order' => $sortOrder,
                'alt_text' => $file->getClientOriginalName(),
                'is_active' => true
            ]);
        }
    }

    /**
     * Format project data for API response
     */
    private function formatProjectData($project)
    {
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
            'main_image' => $project->mainImage ? [
                'id' => $project->mainImage->id,
                'url' => $project->mainImage->image_url,
                'alt_text' => $project->mainImage->alt_text,
                'caption' => $project->mainImage->caption
            ] : null,
            'work_images' => $project->workImages->map(function ($image) {
                return [
                    'id' => $image->id,
                    'url' => $image->image_url,
                    'alt_text' => $image->alt_text,
                    'caption' => $image->caption,
                    'sort_order' => $image->sort_order
                ];
            }),
            'gallery_images' => $project->galleryImages->map(function ($image) {
                return [
                    'id' => $image->id,
                    'url' => $image->image_url,
                    'alt_text' => $image->alt_text,
                    'caption' => $image->caption,
                    'sort_order' => $image->sort_order
                ];
            }),
            'duration' => $durasi,
            'created_at' => $project->created_at,
            'updated_at' => $project->updated_at,
        ];
    }
}