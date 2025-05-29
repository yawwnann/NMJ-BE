<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactSetting;
use Illuminate\Http\Request;


class ContactSettingController extends Controller
{
    /**
     * Display the single contact settings record.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $contactSettings = ContactSetting::first(); // Ambil record pertama (dan seharusnya satu-satunya)

        if (!$contactSettings) {
            return response()->json([
                'message' => 'Contact settings not found. Please create one in CMS.'
            ], 404);
        }

        return response()->json([
            'message' => 'Contact settings retrieved successfully',
            'data' => $contactSettings
        ]);
    }
}