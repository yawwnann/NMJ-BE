# NMJ Backend API

A Laravel-based REST API for managing website content with Cloudinary image integration.

## Features

-   **Hero Sections Management**: Manage website hero sections with images
-   **Services Management**: Manage company services
-   **Team Management**: Manage team members with profile images
-   **Project Management**: Manage projects with status tracking and filtering
-   **Cloudinary Integration**: Automatic image upload and optimization
-   **RESTful API**: Complete CRUD operations for all entities
-   **Validation**: Comprehensive input validation
-   **Error Handling**: Consistent error responses

## Tech Stack

-   **Framework**: Laravel 12
-   **Database**: MySQL/PostgreSQL/SQLite
-   **Image Storage**: Cloudinary
-   **API**: RESTful JSON API
-   **Validation**: Laravel Validation

## Installation

1. **Clone the repository**

```bash
git clone <repository-url>
cd NMJ-BACKEND
```

2. **Install dependencies**

```bash
composer install
```

3. **Environment setup**

```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure database**
   Edit `.env` file with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nmj_backend
DB_USERNAME=root
DB_PASSWORD=
```

5. **Configure Cloudinary**
   Add your Cloudinary credentials to `.env`:

```env
CLOUDINARY_URL=cloudinary://<api_key>:<api_secret>@<cloud_name>
```

6. **Run migrations**

```bash
php artisan migrate
```

7. **Start the server**

```bash
php artisan serve
```

## API Endpoints

### Base URL

```
http://localhost:8000/api
```

### Hero Sections

-   `GET /hero-sections` - Get all hero sections
-   `POST /hero-sections` - Create hero section
-   `GET /hero-sections/{id}` - Get hero section by ID
-   `PUT /hero-sections/{id}` - Update hero section
-   `DELETE /hero-sections/{id}` - Delete hero section

### Services

-   `GET /services` - Get all services
-   `POST /services` - Create service
-   `GET /services/{id}` - Get service by ID
-   `PUT /services/{id}` - Update service
-   `DELETE /services/{id}` - Delete service

### Teams

-   `GET /teams` - Get all team members
-   `POST /teams` - Create team member
-   `GET /teams/{id}` - Get team member by ID
-   `PUT /teams/{id}` - Update team member
-   `DELETE /teams/{id}` - Delete team member

### Projects

-   `GET /projects` - Get all projects
-   `POST /projects` - Create project
-   `GET /projects/statistics` - Get project statistics
-   `GET /projects/{id}` - Get project by ID
-   `PUT /projects/{id}` - Update project
-   `DELETE /projects/{id}` - Delete project

## Database Schema

### Hero Sections

-   `id` - Primary key
-   `title` - Hero section title
-   `description` - Hero section description
-   `image_url` - Cloudinary image URL
-   `is_active` - Active status
-   `created_at` - Creation timestamp
-   `updated_at` - Update timestamp

### Services

-   `id` - Primary key
-   `title` - Service title
-   `description` - Service description
-   `is_active` - Active status
-   `created_at` - Creation timestamp
-   `updated_at` - Update timestamp

### Teams

-   `id` - Primary key
-   `name` - Team member name
-   `position` - Team member position
-   `phone` - Phone number
-   `email` - Email address
-   `address` - Address
-   `image_url` - Profile image URL
-   `is_active` - Active status
-   `created_at` - Creation timestamp
-   `updated_at` - Update timestamp

### Projects

-   `id` - Primary key
-   `title` - Project title
-   `location` - Project location
-   `description` - Project description
-   `construction_category` - Project construction category (e.g. Gedung, Jalan, dll)
-   `start_date` - Project start date
-   `end_date` - Project end date (nullable, diisi jika tidak ongoing)
-   `is_ongoing` - Ongoing status (boolean)
-   `status` - Project status (planning, in_progress, completed, on_hold, cancelled)
-   `image_url` - Project image URL (Cloudinary)
-   `is_active` - Active status
-   `created_at` - Creation timestamp
-   `updated_at` - Update timestamp

**Note:**

-   `duration` tidak disimpan di database, tapi dihitung otomatis dari `start_date` dan `end_date` (atau hari ini jika ongoing) di API response.

## Cloudinary Integration

The API automatically handles image uploads to Cloudinary:

1. **Upload Process**: Images are validated and uploaded to Cloudinary
2. **Optimization**: Cloudinary automatically optimizes images
3. **CDN**: Images are served through Cloudinary's global CDN
4. **Cleanup**: Old images are automatically deleted when updated

### Supported Image Formats

-   JPEG/JPG
-   PNG
-   GIF
-   WebP
-   SVG

### File Size Limit

-   Maximum: 10MB per file

## Usage Examples

### Create Hero Section with Image

```javascript
const formData = new FormData();
formData.append("title", "Welcome to Our Company");
formData.append("description", "We provide excellent services");
formData.append("image", fileInput.files[0]);

fetch("/api/hero-sections", {
    method: "POST",
    body: formData,
})
    .then((response) => response.json())
    .then((data) => console.log(data));
```

### Get All Projects

```javascript
fetch("/api/projects")
    .then((response) => response.json())
    .then((data) => {
        if (data.success) {
            console.log(data.data);
        }
    });
```

### Filter Projects

```javascript
fetch("/api/projects?status=completed&category=web-development")
    .then((response) => response.json())
    .then((data) => console.log(data.data));
```

## Response Format

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

## Testing

### Using cURL

```bash
# Create hero section
curl -X POST http://localhost:8000/api/hero-sections \
  -F "title=Welcome" \
  -F "description=Description" \
  -F "image=@/path/to/image.jpg"

# Get all hero sections
curl -X GET http://localhost:8000/api/hero-sections
```

### Using Postman

1. Import the collection from `test_api_examples.md`
2. Set base URL to `http://localhost:8000/api`
3. Use form-data for file uploads

## Development

### Running Tests

```bash
php artisan test
```

### Code Style

```bash
./vendor/bin/pint
```

### Database Seeding

```bash
php artisan db:seed
```

## Deployment

1. **Production Environment**

```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

2. **Environment Variables**
   Make sure to set production values in `.env`:

```env
APP_ENV=production
APP_DEBUG=false
CLOUDFLARE_ACCOUNT_ID=your_production_account_id
CLOUDFLARE_API_TOKEN=your_production_api_token
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Support

For support, please contact the development team or create an issue in the repository.

## Changelog

### v1.0.0

-   Initial release
-   Hero sections management
-   Services management
-   Team management
-   Project management
-   Cloudinary image integration
