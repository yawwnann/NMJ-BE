<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'location',
        'description',
        'category',
        'duration',
        'status',
        'construction_category',
        'start_date',
        'end_date',
        'is_ongoing',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function images()
    {
        return $this->hasMany(ProjectImage::class);
    }

    public function mainImage()
    {
        return $this->hasOne(ProjectImage::class)->where('type', 'main');
    }

    public function workImages()
    {
        return $this->hasMany(ProjectImage::class)->where('type', 'work');
    }

    public function galleryImages()
    {
        return $this->hasMany(ProjectImage::class)->where('type', 'gallery');
    }

    // Helper methods
    public function getMainImageUrl()
    {
        return $this->mainImage?->image_url;
    }

    public function getWorkImages()
    {
        return $this->workImages()->active()->ordered()->get();
    }

    public function getGalleryImages()
    {
        return $this->galleryImages()->active()->ordered()->get();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}