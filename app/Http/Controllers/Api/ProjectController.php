<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display a listing of the projects.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $projects = Project::orderBy('created_at', 'desc')->get(); // Ambil semua proyek, urutkan terbaru
        return response()->json([
            'message' => 'Projects retrieved successfully',
            'data' => $projects
        ]);
    }

    /**
     * Display the specified project.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Project $project)
    {
        return response()->json([
            'message' => 'Project retrieved successfully',
            'data' => $project
        ]);
    }
}