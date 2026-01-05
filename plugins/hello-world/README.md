# Hello World Plugin

A simple example plugin that demonstrates the CMS plugin system capabilities.

## Features

- Custom routes (web and API)
- Event hooks (CMS booted, page created)
- Custom service provider
- Business logic service
- Custom block type
- Admin menu integration

## Installation

The plugin is automatically discovered when placed in the `/plugins` directory.

## Usage

### API Endpoints

**Get Greeting:**
```
GET /hello-world
```

**Get Custom Greeting:**
```
GET /hello-world/greet/{name}
```

**Get Stats (Authenticated):**
```
GET /api/hello-world/stats
```

### Event Hooks

The plugin listens to:
- `cms.booted` - Logs when CMS boots
- `page.created` - Logs when a page is created

### Custom Block

The plugin registers a `hello-block` that can be used in page content:

```json
{
  "type": "hello-block",
  "attributes": {
    "message": "Custom greeting message"
  }
}
```

## Development

To extend this plugin:

1. Add new routes in `routes/web.php` or `routes/api.php`
2. Add business logic in `HelloWorldService.php`
3. Register new hooks in `HelloWorldServiceProvider.php`
4. Update `plugin.json` with new capabilities

## License

MIT
