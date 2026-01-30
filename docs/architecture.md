# Architecture

This document describes the internal architecture of SvcSitemapBundle.

## Sitemap Generation Flow

1. **SitemapCreator** (`src/Sitemap/SitemapCreator.php`) - Main entry point
2. **SitemapHelper** (`src/Sitemap/SitemapHelper.php`) - Finds static routes from routing config
3. **AddDynamicRoutesEvent** - Dispatched to allow subscribers to add dynamic URLs
4. **RouteParser** (`src/Sitemap/RouteParser.php`) - Parses route configuration:
   - Checks for route options first
   - Falls back to `#[Sitemap]` attribute on controller method
   - Route options take precedence over attributes
5. **CreateXML** (`src/Sitemap/CreateXML.php`) - Generates and validates XML output

## Robots.txt Generation Flow

1. **RobotsCreator** (`src/Robots/RobotsCreator.php`) - Main entry point
2. **RobotsHelper** (`src/Robots/RobotsHelper.php`) - Finds static routes and creates text
3. **AddRobotsTxtEvent** - Dispatched for dynamic content
4. **RobotsRouteParser** (`src/Robots/RobotsRouteParser.php`) - Parses route configuration

## Key Entities

### RouteOptions (`src/Entity/RouteOptions.php`)

Represents a URL entry for sitemap.xml with properties:
- `routeName`, `url`, `lastMod`, `priority`, `changeFreq`, `routeParam`, `alternates`

Used by both static route definitions and dynamic event subscribers.

### RobotsOptions (`src/Entity/RobotsOptions.php`)

Represents robots.txt rule definitions for route-specific directives.

## Event System

### AddDynamicRoutesEvent

Allows adding dynamic URLs to sitemap. Event subscribers call `$event->addUrlToContainer(RouteOptions)`.

See [Dynamic Routes](04-dynamic_routes.md) for implementation examples.

### AddRobotsTxtEvent

Allows adding dynamic robots.txt rules.

See [Dynamic Robots](07-robots_dynamic.md) for implementation examples.

## Console Commands

| Command | Description |
|---------|-------------|
| `svc:sitemap:create_xml` | Creates sitemap.xml (options: --path, --file, --gzip) |
| `svc:robots:create_txt` | Creates robots.txt (options: --path, --file) |

## Exceptions

All exceptions extend `_SitemapException` for easy catching:

| Exception | When thrown |
|-----------|-------------|
| `SitemapTooLargeException` | Sitemap exceeds 50,000 URLs or 50MB |
| `CannotWriteSitemapXML` | File write fails |
| `TranslationNotEnabled` | Translation required but not enabled |
| `RobotsTranslationNotEnabled` | Robots translation required but not enabled |
| `RobotsFilenameMissing` | Robots filename not configured |

## Validation

The sitemap generator automatically validates:
- Maximum 50,000 URLs per sitemap
- Maximum 50 MB uncompressed size
- Valid UTF-8 encoding for all content
- Secure URL schemes (only http/https allowed, prevents XSS)
