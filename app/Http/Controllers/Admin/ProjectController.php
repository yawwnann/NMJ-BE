<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
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
            'construction_category' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_ongoing' => 'boolean',
            'status' => 'required|in:planning,in_progress,completed,on_hold,cancelled',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
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
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $result = $this->cloudinaryService->uploadImage($file);
            if ($result && isset($result['url'])) {
                $data['image_url'] = $result['url'];
                $data['cloudinary_public_id'] = $result['public_id'] ?? null;
            } else {
                return back()->withErrors(['image' => 'Failed to upload image to Cloudinary'])->withInput();
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
            'construction_category' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_ongoing' => 'boolean',
            'status' => 'required|in:planning,in_progress,completed,on_hold,cancelled',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
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
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $result = $this->cloudinaryService->uploadImage($file);
            if ($result && isset($result['url'])) {
                $data['image_url'] = $result['url'];
                $data['cloudinary_public_id'] = $result['public_id'] ?? null;
            } else {
                return back()->withErrors(['image' => 'Failed to upload image to Cloudinary'])->withInput();
            }
        }
        $project->update($data);
        return redirect()->route('admin.project.index')->with('success', 'Project updated successfully!');
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('admin.project.index')->with('success', 'Project deleted successfully!');
    }
}