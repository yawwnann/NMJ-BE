# API Documentation

## Base URL

```
http://localhost:8000/api
```

## Authentication

Currently, the API doesn't require authentication. In production, you should implement proper authentication.

## Cloudflare Images Configuration

Before using the API, make sure to configure Cloudflare Images:

1. Get your Cloudflare Account ID and API Token
2. Add these to your `.env` file:

```
CLOUDFLARE_ACCOUNT_ID=your_account_id
CLOUDFLARE_API_TOKEN=your_api_token
```

## Endpoints

### Hero Sections

#### Get All Hero Sections

```http
GET /api/hero-sections
```

**Response:**

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "title": "Welcome to Our Company",
            "description": "We provide excellent services...",
            "image_url": "https://imagedelivery.net/...",
            "cloudflare_image_id": "abc123",
            "is_active": true,
            "created_at": "2024-01-01T00:00:00.000000Z",
            "updated_at": "2024-01-01T00:00:00.000000Z"
        }
    ],
    "message": "Hero sections retrieved successfully"
}
```

#### Create Hero Section

```http
POST /api/hero-sections
Content-Type: multipart/form-data
```

**Request Body:**

```form-data
title: "Welcome to Our Company"
description: "We provide excellent services..."
image: [file]
is_active: true
```

**Response:**

```json
{
    "success": true,
    "data": {
        "id": 1,
        "title": "Welcome to Our Company",
        "description": "We provide excellent services...",
        "image_url": "https://imagedelivery.net/...",
        "cloudflare_image_id": "abc123",
        "is_active": true,
        "created_at": "2024-01-01T00:00:00.000000Z",
        "updated_at": "2024-01-01T00:00:00.000000Z"
    },
    "message": "Hero section created successfully"
}
```

#### Get Hero Section by ID

```http
GET /api/hero-sections/{id}
```

#### Update Hero Section

```http
PUT /api/hero-sections/{id}
Content-Type: multipart/form-data
```

#### Delete Hero Section

```http
DELETE /api/hero-sections/{id}
```

### Services

#### Get All Services

```http
GET /api/services
```

**Response:**

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "title": "Web Development",
            "description": "We create modern web applications...",
            "is_active": true,
            "created_at": "2024-01-01T00:00:00.000000Z",
            "updated_at": "2024-01-01T00:00:00.000000Z"
        }
    ],
    "message": "Services retrieved successfully"
}
```

#### Create Service

```http
POST /api/services
Content-Type: application/json
```

**Request Body:**

```json
{
    "title": "Web Development",
    "description": "We create modern web applications...",
    "is_active": true
}
```

#### Get Service by ID

```http
GET /api/services/{id}
```

#### Update Service

```http
PUT /api/services/{id}
Content-Type: application/json
```

#### Delete Service

```http
DELETE /api/services/{id}
```

### Teams

#### Get All Team Members

```http
GET /api/teams
```

**Response:**

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "John Doe",
            "position": "Senior Developer",
            "phone": "+1234567890",
            "email": "john@example.com",
            "address": "123 Main St, City, Country",
            "image_url": "https://imagedelivery.net/...",
            "cloudflare_image_id": "abc123",
            "is_active": true,
            "created_at": "2024-01-01T00:00:00.000000Z",
            "updated_at": "2024-01-01T00:00:00.000000Z"
        }
    ],
    "message": "Team members retrieved successfully"
}
```

#### Create Team Member

```http
POST /api/teams
Content-Type: multipart/form-data
```

**Request Body:**

```form-data
name: "John Doe"
position: "Senior Developer"
phone: "+1234567890"
email: "john@example.com"
address: "123 Main St, City, Country"
image: [file]
is_active: true
```

#### Get Team Member by ID

```http
GET /api/teams/{id}
```

#### Update Team Member

```http
PUT /api/teams/{id}
Content-Type: multipart/form-data
```

#### Delete Team Member

```http
DELETE /api/teams/{id}
```

### Projects

#### Get All Projects

```http
GET /api/projects
```

**Query Parameters:**

-   `status`: Filter by status (planning, in_progress, completed, on_hold, cancelled)
-   `category`: Filter by category

**Example:**

```http
GET /api/projects?status=completed&category=web-development
```

**Response:**

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "title": "E-commerce Website",
            "location": "Jakarta, Indonesia",
            "description": "Modern e-commerce platform...",
            "category": "web-development",
            "duration": "3 months",
            "status": "completed",
            "image_url": "https://imagedelivery.net/...",
            "cloudflare_image_id": "abc123",
            "is_active": true,
            "created_at": "2024-01-01T00:00:00.000000Z",
            "updated_at": "2024-01-01T00:00:00.000000Z"
        }
    ],
    "message": "Projects retrieved successfully"
}
```

#### Create Project

```http
POST /api/projects
Content-Type: multipart/form-data
```

**Request Body:**

```form-data
title: "E-commerce Website"
location: "Jakarta, Indonesia"
description: "Modern e-commerce platform..."
category: "web-development"
duration: "3 months"
status: "completed"
image: [file]
is_active: true
```

#### Get Project Statistics

```http
GET /api/projects/statistics
```

**Response:**

```json
{
    "success": true,
    "data": {
        "total": 10,
        "planning": 2,
        "in_progress": 3,
        "completed": 4,
        "on_hold": 1,
        "cancelled": 0
    },
    "message": "Project statistics retrieved successfully"
}
```

#### Get Project by ID

```http
GET /api/projects/{id}
```

#### Update Project

```http
PUT /api/projects/{id}
Content-Type: multipart/form-data
```

#### Delete Project

```http
DELETE /api/projects/{id}
```

## Error Responses

All endpoints return consistent error responses:

```json
{
    "success": false,
    "message": "Error message",
    "errors": {
        "field": ["Validation error message"]
    }
}
```

**HTTP Status Codes:**

-   `200`: Success
-   `201`: Created
-   `422`: Validation Error
-   `500`: Server Error

## Image Upload

### Supported Formats

-   JPEG/JPG
-   PNG
-   GIF
-   WebP
-   SVG

### File Size Limit

-   Maximum: 10MB per file

### Image Storage

All images are automatically uploaded to Cloudflare Images and optimized for delivery.

## Setup Instructions

1. **Install Dependencies:**

```bash
composer install
```

2. **Environment Setup:**

```bash
cp .env.example .env
php artisan key:generate
```

3. **Database Setup:**

```bash
php artisan migrate
```

4. **Cloudflare Configuration:**
   Add to `.env`:

```
CLOUDFLARE_ACCOUNT_ID=your_account_id
CLOUDFLARE_API_TOKEN=your_api_token
```

5. **Start Server:**

```bash
php artisan serve
```

## Testing with Postman

1. Import the collection
2. Set the base URL to `http://localhost:8000/api`
3. For file uploads, use `form-data` in the request body
4. Set the `Content-Type` header to `multipart/form-data` for file uploads

## Frontend Integration

### Example: Upload Image with Form Data

```javascript
const formData = new FormData();
formData.append("title", "My Title");
formData.append("description", "My Description");
formData.append("image", fileInput.files[0]);

fetch("/api/hero-sections", {
    method: "POST",
    body: formData,
})
    .then((response) => response.json())
    .then((data) => console.log(data));
```

### Example: Get Data

```javascript
fetch("/api/hero-sections")
    .then((response) => response.json())
    .then((data) => {
        if (data.success) {
            console.log(data.data);
        }
    });
```
