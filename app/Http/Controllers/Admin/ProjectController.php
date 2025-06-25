<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Services\CloudflareImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    protected $cloudflareService;

    public function __construct(CloudflareImageService $cloudflareService)
    {
        $this->cloudflareService = $cloudflareService;
    }

    public function index()
    {
        $projects = Project::orderByDesc('id')->get();
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
            'category' => 'required|string|max:255',
            'duration' => 'required|string|max:255',
            'status' => 'required|in:planning,in_progress,completed,on_hold,cancelled',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'is_active' => 'boolean',
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $data = $request->only(['title', 'location', 'description', 'category', 'duration', 'status', 'is_active']);
        $data['is_active'] = $request->boolean('is_active', true);
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            if (!$this->cloudflareService->validateFile($file)) {
                return back()->withErrors(['image' => 'Invalid image file'])->withInput();
            }
            $uploadResult = $this->cloudflareService->uploadImage($file);
            if ($uploadResult) {
                $data['image_url'] = $uploadResult['url'];
                $data['cloudflare_image_id'] = $uploadResult['id'];
            } else {
                return back()->withErrors(['image' => 'Failed to upload image to Cloudflare'])->withInput();
            }
        }
        Project::create($data);
        return redirect()->route('admin.project.index')->with('success', 'Project created successfully!');
    }

    public function edit(Project $project)
    {
        return view('admin.project.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:255',
            'duration' => 'required|string|max:255',
            'status' => 'required|in:planning,in_progress,completed,on_hold,cancelled',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'is_active' => 'boolean',
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $data = $request->only(['title', 'location', 'description', 'category', 'duration', 'status', 'is_active']);
        $data['is_active'] = $request->boolean('is_active', true);
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            if (!$this->cloudflareService->validateFile($file)) {
                return back()->withErrors(['image' => 'Invalid image file'])->withInput();
            }
            if ($project->cloudflare_image_id) {
                $this->cloudflareService->deleteImage($project->cloudflare_image_id);
            }
            $uploadResult = $this->cloudflareService->uploadImage($file);
            if ($uploadResult) {
                $data['image_url'] = $uploadResult['url'];
                $data['cloudflare_image_id'] = $uploadResult['id'];
            } else {
                return back()->withErrors(['image' => 'Failed to upload image to Cloudflare'])->withInput();
            }
        }
        $project->update($data);
        return redirect()->route('admin.project.index')->with('success', 'Project updated successfully!');
    }

    public function destroy(Project $project)
    {
        if ($project->cloudflare_image_id) {
            $this->cloudflareService->deleteImage($project->cloudflare_image_id);
        }
        $project->delete();
        return redirect()->route('admin.project.index')->with('success', 'Project deleted successfully!');
    }
}