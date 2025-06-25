<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Services\CloudflareImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TeamController extends Controller
{
    protected $cloudflareService;

    public function __construct(CloudflareImageService $cloudflareService)
    {
        $this->cloudflareService = $cloudflareService;
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
            if (!$this->cloudflareService->validateFile($file)) {
                return back()->withErrors(['image' => 'Invalid image file'])->withInput();
            }
            if ($team->cloudflare_image_id) {
                $this->cloudflareService->deleteImage($team->cloudflare_image_id);
            }
            $uploadResult = $this->cloudflareService->uploadImage($file);
            if ($uploadResult) {
                $data['image_url'] = $uploadResult['url'];
                $data['cloudflare_image_id'] = $uploadResult['id'];
            } else {
                return back()->withErrors(['image' => 'Failed to upload image to Cloudflare'])->withInput();
            }
        }
        $team->update($data);
        return redirect()->route('admin.team.index')->with('success', 'Team member updated successfully!');
    }

    public function destroy(Team $team)
    {
        if ($team->cloudflare_image_id) {
            $this->cloudflareService->deleteImage($team->cloudflare_image_id);
        }
        $team->delete();
        return redirect()->route('admin.team.index')->with('success', 'Team member deleted successfully!');
    }
}