<?php

// app/Models/ContactSetting.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'address',
        'phone',
        'email',
        'Maps_link',
        'office_hours',
        'facebook_url',
        'instagram_url',
        'linkedin_url',
    ];
}