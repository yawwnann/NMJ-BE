# Project Images Structure

## Overview

The project system has been restructured to support multiple images per project with different types and purposes.

## Database Structure

### Projects Table

-   Removed: `image_url`, `cloudflare_image_id` fields
-   All image data is now stored in the separate `project_images` table

### Project Images Table

```sql
project_images:
- id (primary key)
- project_id (foreign key to projects)
- image_url (Cloudinary URL)
- cloudinary_public_id (Cloudinary public ID)
- type (enum: 'main', 'work', 'gallery')
- alt_text (optional)
- caption (optional)
- sort_order (for ordering)
- is_active (boolean)
- timestamps
```

## Image Types

### 1. Main Image (`type = 'main'`)

-   **Purpose**: Displayed as the primary thumbnail on the homepage
-   **Quantity**: Only one per project (enforced by unique constraint)
-   **Usage**: Featured image, project card thumbnails, main project display

### 2. Work Images (`type = 'work'`)

-   **Purpose**: Show progress and work in progress sections
-   **Quantity**: Multiple images allowed
-   **Usage**: Construction progress, work documentation, before/after shots

### 3. Gallery Images (`type = 'gallery'`)

-   **Purpose**: Additional project photos for detailed view
-   **Quantity**: Multiple images allowed
-   **Usage**: Project gallery, detailed views, additional documentation

## Model Relationships

### Project Model

```php
// Relationships
public function images() // All images
public function mainImage() // Single main image
public function workImages() // Multiple work images
public function galleryImages() // Multiple gallery images

// Helper methods
public function getMainImageUrl() // Get main image URL
public function getWorkImages() // Get ordered work images
public function getGalleryImages() // Get ordered gallery images
```

### ProjectImage Model

```php
// Relationships
public function project() // Belongs to project

// Scopes
scopeActive() // Only active images
scopeByType($type) // Filter by image type
scopeMain() // Only main images
scopeWork() // Only work images
scopeGallery() // Only gallery images
scopeOrdered() // Order by sort_order
```

## API Endpoints

### Project Endpoints

-   `GET /api/projects` - List all projects with images
-   `POST /api/projects` - Create project with images
-   `GET /api/projects/{id}` - Get specific project with images
-   `PUT /api/projects/{id}` - Update project with images
-   `DELETE /api/projects/{id}` - Delete project and all images

### Image Endpoints

-   `DELETE /api/project-images/{id}` - Delete specific image

## API Response Format

```json
{
    "success": true,
    "data": {
        "id": 1,
        "title": "Project Title",
        "location": "Project Location",
        "description": "Project Description",
        "construction_category": "Gedung",
        "start_date": "2024-01-01",
        "end_date": "2024-12-31",
        "is_ongoing": false,
        "status": "completed",
        "is_active": true,
        "main_image": {
            "id": 1,
            "url": "https://res.cloudinary.com/...",
            "alt_text": "Main project image",
            "caption": "Project main photo"
        },
        "work_images": [
            {
                "id": 2,
                "url": "https://res.cloudinary.com/...",
                "alt_text": "Work progress 1",
                "caption": "Construction progress",
                "sort_order": 0
            }
        ],
        "gallery_images": [
            {
                "id": 3,
                "url": "https://res.cloudinary.com/...",
                "alt_text": "Gallery image 1",
                "caption": "Additional project photo",
                "sort_order": 0
            }
        ],
        "duration": "12 bulan 1 hari",
        "created_at": "2024-01-01T00:00:00.000000Z",
        "updated_at": "2024-01-01T00:00:00.000000Z"
    },
    "message": "Project retrieved successfully"
}
```

## Admin Interface

### Create Project

-   Separate upload sections for main, work, and gallery images
-   Clear labeling and descriptions for each image type
-   Multiple file selection for work and gallery images

### Edit Project

-   Display current images by type with delete options
-   Upload new images for each type
-   Replace main image functionality

### Image Management

-   Delete individual images
-   Visual preview of all images
-   Type-based organization

## Frontend Usage

### Homepage Display

```php
// Get projects with main images for homepage
$projects = Project::active()->with('mainImage')->get();

// Display main image
@if($project->mainImage)
    <img src="{{ $project->mainImage->image_url }}" alt="{{ $project->mainImage->alt_text }}">
@endif
```

### Work Progress Section

```php
// Get work images for progress display
$workImages = $project->getWorkImages();

// Display work images
@foreach($workImages as $image)
    <img src="{{ $image->image_url }}" alt="{{ $image->alt_text }}">
@endforeach
```

### Project Gallery

```php
// Get gallery images for detailed view
$galleryImages = $project->getGalleryImages();

// Display gallery
@foreach($galleryImages as $image)
    <img src="{{ $image->image_url }}" alt="{{ $image->alt_text }}">
@endforeach
```

## Migration Notes

1. **Backup existing data** before running migrations
2. **Run migrations in order**:
    - `2025_01_15_000001_create_project_images_table`
    - `2025_01_15_000002_remove_image_fields_from_projects_table`
3. **Data migration**: If you have existing projects with images, you'll need to create a data migration script to move existing `image_url` data to the new `project_images` table as `main` type images.

## Benefits

1. **Flexibility**: Admin can upload as many images as needed
2. **Organization**: Clear separation of image purposes
3. **Performance**: Efficient loading with relationships
4. **Scalability**: Easy to add new image types in the future
5. **User Experience**: Better organized content for different sections
