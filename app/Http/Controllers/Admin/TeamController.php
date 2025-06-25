<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Services\CloudinaryNativeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TeamController extends Controller
{
    protected $cloudinaryService;

    public function __construct()
    {
        $this->cloudinaryService = new CloudinaryNativeService();
    }

    public function index()
    {
        $teams = Team::orderByDesc('id')->get();
        return view('admin.team.index', compact('teams'));
    }

    public function create()
    {
        return view('admin.team.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'address' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'is_active' => 'boolean',
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $data = $request->only(['name', 'position', 'phone', 'email', 'address', 'is_active']);
        $data['is_active'] = $request->boolean('is_active', true);
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
        Team::create($data);
        return redirect()->route('admin.team.index')->with('success', 'Team member created successfully!');
    }

    public function edit(Team $team)
    {
        return view('admin.team.edit', compact('team'));
    }

    public function update(Request $request, Team $team)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'address' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'is_active' => 'boolean',
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $data = $request->only(['name', 'position', 'phone', 'email', 'address', 'is_active']);
        $data['is_active'] = $request->boolean('is_active', true);
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
        $team->update($data);
        return redirect()->route('admin.team.index')->with('success', 'Team member updated successfully!');
    }

    public function destroy(Team $team)
    {
        if ($team->cloudinary_public_id) {
            $this->cloudinaryService->deleteImage($team->cloudinary_public_id);
        }
        $team->delete();
        return redirect()->route('admin.team.index')->with('success', 'Team member deleted successfully!');
    }
}