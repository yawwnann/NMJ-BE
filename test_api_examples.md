# API Testing Examples

## Setup

1. Start the Laravel server:

```bash
php artisan serve
```

2. Configure Cloudflare Images in `.env`:

```
CLOUDFLARE_ACCOUNT_ID=your_account_id
CLOUDFLARE_API_TOKEN=your_api_token
```

## Testing with cURL

### Hero Sections

#### Create Hero Section

```bash
curl -X POST http://localhost:8000/api/hero-sections \
  -F "title=Welcome to Our Company" \
  -F "description=We provide excellent web development and design services" \
  -F "image=@/path/to/your/image.jpg" \
  -F "is_active=true"
```

#### Get All Hero Sections

```bash
curl -X GET http://localhost:8000/api/hero-sections
```

#### Update Hero Section

```bash
curl -X PUT http://localhost:8000/api/hero-sections/1 \
  -F "title=Updated Title" \
  -F "description=Updated description" \
  -F "image=@/path/to/new/image.jpg" \
  -F "_method=PUT"
```

#### Delete Hero Section

```bash
curl -X DELETE http://localhost:8000/api/hero-sections/1
```

### Services

#### Create Service

```bash
curl -X POST http://localhost:8000/api/services \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Web Development",
    "description": "We create modern web applications using the latest technologies",
    "is_active": true
  }'
```

#### Get All Services

```bash
curl -X GET http://localhost:8000/api/services
```

#### Update Service

```bash
curl -X PUT http://localhost:8000/api/services/1 \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Updated Web Development",
    "description": "Updated description",
    "is_active": true
  }'
```

### Teams

#### Create Team Member

```bash
curl -X POST http://localhost:8000/api/teams \
  -F "name=John Doe" \
  -F "position=Senior Developer" \
  -F "phone=+1234567890" \
  -F "email=john@example.com" \
  -F "address=123 Main St, Jakarta, Indonesia" \
  -F "image=@/path/to/profile.jpg" \
  -F "is_active=true"
```

#### Get All Team Members

```bash
curl -X GET http://localhost:8000/api/teams
```

#### Update Team Member

```bash
curl -X PUT http://localhost:8000/api/teams/1 \
  -F "name=John Updated" \
  -F "position=Lead Developer" \
  -F "phone=+1234567890" \
  -F "email=john.updated@example.com" \
  -F "address=456 New St, Jakarta, Indonesia" \
  -F "image=@/path/to/new-profile.jpg" \
  -F "_method=PUT"
```

### Projects

#### Create Project

```bash
curl -X POST http://localhost:8000/api/projects \
  -F "title=E-commerce Website" \
  -F "location=Jakarta, Indonesia" \
  -F "description=Modern e-commerce platform with payment integration" \
  -F "category=web-development" \
  -F "duration=3 months" \
  -F "status=completed" \
  -F "image=@/path/to/project-image.jpg" \
  -F "is_active=true"
```

#### Get All Projects

```bash
curl -X GET http://localhost:8000/api/projects
```

#### Get Projects with Filters

```bash
# Filter by status
curl -X GET "http://localhost:8000/api/projects?status=completed"

# Filter by category
curl -X GET "http://localhost:8000/api/projects?category=web-development"

# Filter by both
curl -X GET "http://localhost:8000/api/projects?status=completed&category=web-development"
```

#### Get Project Statistics

```bash
curl -X GET http://localhost:8000/api/projects/statistics
```

#### Update Project

```bash
curl -X PUT http://localhost:8000/api/projects/1 \
  -F "title=Updated E-commerce Website" \
  -F "location=Bandung, Indonesia" \
  -F "description=Updated project description" \
  -F "category=mobile-development" \
  -F "duration=4 months" \
  -F "status=in_progress" \
  -F "image=@/path/to/updated-image.jpg" \
  -F "_method=PUT"
```

## Testing with Postman

### Import Collection

1. Open Postman
2. Click "Import"
3. Create a new collection called "Laravel API"
4. Add the following requests:

### Hero Sections Collection

#### Create Hero Section

-   **Method**: POST
-   **URL**: `http://localhost:8000/api/hero-sections`
-   **Body**: form-data
    -   title: Welcome to Our Company
    -   description: We provide excellent services
    -   image: [file]
    -   is_active: true

#### Get Hero Sections

-   **Method**: GET
-   **URL**: `http://localhost:8000/api/hero-sections`

#### Update Hero Section

-   **Method**: PUT
-   **URL**: `http://localhost:8000/api/hero-sections/1`
-   **Body**: form-data
    -   title: Updated Title
    -   description: Updated description
    -   image: [file]
    -   \_method: PUT

### Services Collection

#### Create Service

-   **Method**: POST
-   **URL**: `http://localhost:8000/api/services`
-   **Headers**: Content-Type: application/json
-   **Body**: raw (JSON)

```json
{
    "title": "Web Development",
    "description": "We create modern web applications",
    "is_active": true
}
```

### Teams Collection

#### Create Team Member

-   **Method**: POST
-   **URL**: `http://localhost:8000/api/teams`
-   **Body**: form-data
    -   name: John Doe
    -   position: Senior Developer
    -   phone: +1234567890
    -   email: john@example.com
    -   address: 123 Main St, Jakarta
    -   image: [file]
    -   is_active: true

### Projects Collection

#### Create Project

-   **Method**: POST
-   **URL**: `http://localhost:8000/api/projects`
-   **Body**: form-data
    -   title: E-commerce Website
    -   location: Jakarta, Indonesia
    -   description: Modern e-commerce platform
    -   category: web-development
    -   duration: 3 months
    -   status: completed
    -   image: [file]
    -   is_active: true

#### Get Project Statistics

-   **Method**: GET
-   **URL**: `http://localhost:8000/api/projects/statistics`

## Expected Responses

### Success Response

```json
{
    "success": true,
    "data": {
        "id": 1,
        "title": "Example",
        "created_at": "2024-01-01T00:00:00.000000Z",
        "updated_at": "2024-01-01T00:00:00.000000Z"
    },
    "message": "Created successfully"
}
```

### Error Response

```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "title": ["The title field is required."]
    }
}
```

## Testing Checklist

-   [ ] Create hero section with image
-   [ ] Get all hero sections
-   [ ] Update hero section
-   [ ] Delete hero section
-   [ ] Create service
-   [ ] Get all services
-   [ ] Update service
-   [ ] Delete service
-   [ ] Create team member with image
-   [ ] Get all team members
-   [ ] Update team member
-   [ ] Delete team member
-   [ ] Create project with image
-   [ ] Get all projects
-   [ ] Filter projects by status
-   [ ] Filter projects by category
-   [ ] Get project statistics
-   [ ] Update project
-   [ ] Delete project

## Troubleshooting

### Common Issues

1. **Image upload fails**: Check Cloudflare configuration in `.env`
2. **Validation errors**: Ensure all required fields are provided
3. **404 errors**: Make sure the server is running and routes are correct
4. **Database errors**: Run `php artisan migrate` to create tables

### Debug Commands

```bash
# Check routes
php artisan route:list

# Clear cache
php artisan config:clear
php artisan cache:clear

# Check logs
tail -f storage/logs/laravel.log
```
