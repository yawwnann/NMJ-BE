<?php

// app/Models/Project.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'location',
        'client',
        'short_description',
        'description',
        'thumbnail',
        'images',
        'status',
    ];

    protected $casts = [
        'images' => 'array', // Penting untuk menyimpan multiple images sebagai JSON
    ];
}