<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use Illuminate\Http\Request;

class TeamMemberController extends Controller
{
    /**
     * Display a listing of the team members.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $teamMembers = TeamMember::orderBy('name')->get(); // Ambil semua anggota tim, urutkan berdasarkan nama
        return response()->json([
            'message' => 'Team members retrieved successfully',
            'data' => $teamMembers
        ]);
    }

    /**
     * Display the specified team member.
     *
     * @param  \App\Models\TeamMember  $teamMember
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(TeamMember $teamMember)
    {
        return response()->json([
            'message' => 'Team member retrieved successfully',
            'data' => $teamMember
        ]);
    }
}