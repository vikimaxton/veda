# CMS API Documentation

## Base URL
```
http://localhost:8000/api/cms
```

All API endpoints require authentication via Laravel Sanctum. Include the bearer token in the Authorization header:
```
Authorization: Bearer YOUR_TOKEN
```

---

## Pages API

### List Pages
```http
GET /api/cms/pages
```

**Query Parameters:**
- `status` (string): Filter by status (draft, published, private)
- `parent_id` (uuid): Filter by parent page
- `search` (string): Search by title
- `sort_by` (string): Sort field (default: created_at)
- `sort_order` (string): Sort direction (asc, desc)
- `per_page` (integer): Items per page (default: 20)

**Response:**
```json
{
  "data": [
    {
      "id": "uuid",
      "title": "Page Title",
      "slug": "page-slug",
      "status": "published",
      "template": "home",
      "content_schema": [...],
      "seo_meta": {...},
      "parent_id": null,
      "created_by": "uuid",
      "created_at": "2026-01-05T00:00:00.000000Z",
      "updated_at": "2026-01-05T00:00:00.000000Z",
      "is_published": true,
      "has_children": false,
      "url": "/page-slug"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 20,
    "total": 100
  }
}
```

### Create Page
```http
POST /api/cms/pages
```

**Request Body:**
```json
{
  "title": "New Page",
  "slug": "new-page",
  "status": "draft",
  "template": "home",
  "content_schema": [
    {
      "type": "heading",
      "attributes": {
        "level": 1,
        "content": "Welcome"
      }
    },
    {
      "type": "paragraph",
      "attributes": {
        "content": "Page content here"
      }
    }
  ],
  "seo_meta": {
    "title": "SEO Title",
    "description": "SEO Description",
    "og_image": "https://example.com/image.jpg"
  },
  "parent_id": null
}
```

**Response:** `201 Created`

### Get Page
```http
GET /api/cms/pages/{id}
```

### Update Page
```http
PUT /api/cms/pages/{id}
```

### Delete Page
```http
DELETE /api/cms/pages/{id}
```

### Publish Page
```http
POST /api/cms/pages/{id}/publish
```

### Unpublish Page
```http
POST /api/cms/pages/{id}/unpublish
```

### Get Page Versions
```http
GET /api/cms/pages/{id}/versions
```

### Restore Page Version
```http
POST /api/cms/pages/{id}/restore/{versionId}
```

---

## Themes API

### List Themes
```http
GET /api/cms/themes
```

**Response:**
```json
{
  "data": [
    {
      "id": "uuid",
      "name": "Default Theme",
      "slug": "default",
      "version": "1.0.0",
      "description": "A clean, modern default theme",
      "author": "CMS Team",
      "is_active": true,
      "templates": {...},
      "settings": {...}
    }
  ]
}
```

### Get Theme
```http
GET /api/cms/themes/{slug}
```

### Activate Theme
```http
POST /api/cms/themes/{slug}/activate
```

### Preview Theme
```http
GET /api/cms/themes/{slug}/preview
```

---

## Plugins API

### List Plugins
```http
GET /api/cms/plugins
```

**Response:**
```json
{
  "data": [
    {
      "id": "uuid",
      "name": "Hello World Plugin",
      "slug": "hello-world",
      "version": "1.0.0",
      "description": "Example plugin",
      "author": "CMS Team",
      "is_active": false,
      "is_installed": true,
      "permissions": ["view_hello"],
      "hooks": ["cms.booted", "page.created"],
      "blocks": [...]
    }
  ]
}
```

### Get Plugin
```http
GET /api/cms/plugins/{slug}
```

### Install Plugin
```http
POST /api/cms/plugins/{slug}/install
```

### Activate Plugin
```http
POST /api/cms/plugins/{slug}/activate
```

### Deactivate Plugin
```http
POST /api/cms/plugins/{slug}/deactivate
```

### Uninstall Plugin
```http
DELETE /api/cms/plugins/{slug}
```

---

## Roles API

### List Roles
```http
GET /api/cms/roles
```

**Response:**
```json
{
  "data": [
    {
      "id": "uuid",
      "name": "Admin",
      "slug": "admin",
      "description": "Administrative access",
      "permissions": [...],
      "users_count": 5
    }
  ]
}
```

### Create Role
```http
POST /api/cms/roles
```

**Request Body:**
```json
{
  "name": "Custom Role",
  "slug": "custom-role",
  "description": "Custom role description",
  "permissions": ["uuid1", "uuid2"]
}
```

### Get Role
```http
GET /api/cms/roles/{id}
```

### Update Role
```http
PUT /api/cms/roles/{id}
```

### Delete Role
```http
DELETE /api/cms/roles/{id}
```

---

## Permissions API

### List All Permissions
```http
GET /api/cms/permissions
```

### Get Permissions by Category
```http
GET /api/cms/permissions/categories
```

**Response:**
```json
{
  "data": [
    {
      "category": "pages",
      "permissions": [
        {
          "id": "uuid",
          "name": "View Pages",
          "slug": "view_pages",
          "category": "pages",
          "description": null
        }
      ]
    }
  ]
}
```

---

## Settings API

### Get All Settings
```http
GET /api/cms/settings
```

### Get Settings by Group
```http
GET /api/cms/settings/{group}
```

### Update Multiple Settings
```http
PUT /api/cms/settings
```

**Request Body:**
```json
{
  "settings": [
    {
      "key": "site_name",
      "value": "My CMS",
      "group": "general"
    },
    {
      "key": "site_description",
      "value": "A modern CMS",
      "group": "general"
    }
  ]
}
```

### Update Single Setting
```http
PUT /api/cms/settings/{key}
```

**Request Body:**
```json
{
  "value": "New Value",
  "group": "general"
}
```

---

## Blocks API

### Get Available Blocks
```http
GET /api/cms/blocks
```

**Response:**
```json
{
  "heading": {
    "name": "Heading",
    "category": "text",
    "icon": "heading",
    "attributes": {
      "level": {"type": "number", "default": 2},
      "content": {"type": "string", "default": ""}
    }
  },
  "paragraph": {...},
  "image": {...},
  "button": {...},
  "spacer": {...}
}
```

---

## Error Responses

All endpoints return standard error responses:

**401 Unauthorized:**
```json
{
  "message": "Unauthenticated."
}
```

**403 Forbidden:**
```json
{
  "message": "This action is unauthorized."
}
```

**404 Not Found:**
```json
{
  "message": "Resource not found."
}
```

**422 Validation Error:**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "title": ["The title field is required."]
  }
}
```

---

## Authentication

To obtain an API token, use Laravel Sanctum:

```http
POST /login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password"
}
```

Then use the token in subsequent requests:
```http
Authorization: Bearer {token}
```

---

## Rate Limiting

API requests are rate-limited to:
- **60 requests per minute** for authenticated users
- **10 requests per minute** for unauthenticated users

Rate limit headers are included in responses:
```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
```

---

## Pagination

List endpoints support pagination with these parameters:
- `page` (integer): Page number (default: 1)
- `per_page` (integer): Items per page (default: 20, max: 100)

Pagination metadata is included in the response:
```json
{
  "data": [...],
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 20,
    "total": 100
  }
}
```

---

## Content Schema

Pages use a JSON-based content schema with blocks:

### Heading Block
```json
{
  "type": "heading",
  "attributes": {
    "level": 1,
    "content": "Heading text"
  }
}
```

### Paragraph Block
```json
{
  "type": "paragraph",
  "attributes": {
    "content": "Paragraph text"
  }
}
```

### Image Block
```json
{
  "type": "image",
  "attributes": {
    "url": "https://example.com/image.jpg",
    "alt": "Image description",
    "caption": "Optional caption"
  }
}
```

### Button Block
```json
{
  "type": "button",
  "attributes": {
    "text": "Click me",
    "url": "/page",
    "variant": "primary"
  }
}
```

### Spacer Block
```json
{
  "type": "spacer",
  "attributes": {
    "height": 40
  }
}
```

---

## Webhooks (Future)

Webhook support is planned for:
- `page.created`
- `page.updated`
- `page.published`
- `plugin.activated`
- `theme.activated`

---

For more information, visit the [GitHub repository](https://github.com/your-repo/cms).
