<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectImage;
use App\Services\CloudinaryNativeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    protected $cloudinaryService;

    public function __construct()
    {
        $this->cloudinaryService = new CloudinaryNativeService();
    }

    public function index()
    {
        $projects = Project::with(['mainImage', 'workImages'])->orderByDesc('id')->get();
        return view('admin.project.index', compact('projects'));
    }

    public function create()
    {
        return view('admin.project.create');
    }

    public function store(Request $request)
    {
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
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
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

        return redirect()->route('admin.project.index')->with('success', 'Project created successfully!');
    }

    public function edit(Project $project)
    {
        $project->load(['mainImage', 'workImages', 'galleryImages']);
        return view('admin.project.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
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
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
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

        return redirect()->route('admin.project.index')->with('success', 'Project updated successfully!');
    }

    public function destroy(Project $project)
    {
        // Delete all associated images
        $project->images()->delete();
        $project->delete();

        return redirect()->route('admin.project.index')->with('success', 'Project deleted successfully!');
    }

    public function deleteImage(ProjectImage $image)
    {
        $image->delete();
        return back()->with('success', 'Image deleted successfully!');
    }

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
}