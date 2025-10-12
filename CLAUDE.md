# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

SvcSitemapBundle is a Symfony bundle that generates XML sitemaps and robots.txt files. It supports both static routes (from Symfony's routing configuration) and dynamic routes (added via event subscribers/listeners).

**Key Features:**
- XML sitemap generation with support for lastmod, changefreq, priority
- PHP Attributes support with `#[Sitemap]` and `#[Robots]` attributes for type-safe configuration (PHP 8+)
- Multi-language support with alternate URLs (hreflang)
- robots.txt generation with optional sitemap reference
- Console commands to create and dump files
- Event-driven architecture for adding dynamic content
- URL validation and security (prevents XSS via javascript:, data: schemes)
- UTF-8 validation for all content
- Automatic size validation (50,000 URLs / 50MB limits)

## Development Commands

### Testing
```bash
# Run all tests with testdox format
composer test
# or
vendor/bin/phpunit --testdox

# Run specific test
vendor/bin/phpunit tests/Unit/Sitemap/CreateXMLTest.php

# Run with coverage
vendor/bin/phpunit --coverage-html coverage/
```

### Code Quality
```bash
# Run PHPStan static analysis (level 7)
composer phpstan
# or
php -d memory_limit=-1 vendor/bin/phpstan analyse -c .phpstan.neon

# Run PHP-CS-Fixer
/opt/homebrew/bin/php-cs-fixer fix

# Check code style without fixing
/opt/homebrew/bin/php-cs-fixer fix --dry-run --diff
```

## Architecture

### Core Components

**Sitemap Generation Flow:**
1. `SitemapCreator` (src/Sitemap/SitemapCreator.php) - Main entry point
2. `SitemapHelper` (src/Sitemap/SitemapHelper.php) - Finds static routes from routing config
3. `AddDynamicRoutesEvent` - Dispatched to allow subscribers to add dynamic URLs
4. `RouteParser` (src/Sitemap/RouteParser.php) - Parses route configuration:
   - Checks for route options first (e.g., `options: ['sitemap' => ...]`)
   - Falls back to `#[Sitemap]` attribute on controller method
   - Route options take precedence over attributes
5. `CreateXML` (src/Sitemap/CreateXML.php) - Generates and validates XML output
   - Validates URL count (max 50,000)
   - Validates each URL (format, UTF-8, scheme)
   - Validates XML size (max 50MB)
   - Throws `SitemapTooLargeException` if limits exceeded

**Robots.txt Generation Flow:**
1. `RobotsCreator` (src/Robots/RobotsCreator.php) - Main entry point
2. `RobotsHelper` (src/Robots/RobotsHelper.php) - Finds static routes and creates text
3. `AddRobotsTxtEvent` - Dispatched for dynamic content
4. `RobotsRouteParser` (src/Robots/RobotsRouteParser.php) - Parses route configuration:
   - Checks for route options first (e.g., `options: ['robots_txt' => ...]`)
   - Falls back to `#[Robots]` attribute on controller method
   - Route options take precedence over attributes

### Key Entities

**RouteOptions** (src/Entity/RouteOptions.php)
- Represents a URL entry for sitemap.xml
- Properties: routeName, url, lastMod, priority, changeFreq, routeParam, alternates
- Used by both static route definitions and dynamic event subscribers

**RobotsOptions** (src/Entity/RobotsOptions.php)
- Represents robots.txt rule definitions
- Used for route-specific robots directives

### Route Configuration

Routes can be configured in two ways:

**Method 1: Using attributes (recommended for PHP 8+):**

```php
use Svc\SitemapBundle\Attribute\Sitemap;
use Svc\SitemapBundle\Attribute\Robots;
use Svc\SitemapBundle\Enum\ChangeFreq;

#[Route('/path', name: 'route_name')]
#[Sitemap(priority: 0.8, changeFreq: ChangeFreq::WEEKLY, lastMod: '2024-01-01')]
#[Robots(allow: true, userAgents: ['google', 'bing'])]
public function myAction(): Response
{
    //...
}
```

**Method 2: Using route options (works with all PHP versions):**

```php
#[Route('/path', name: 'route_name', options: [
    'sitemap' => [
        'priority' => 0.8,
        'changefreq' => 'weekly',
        'lastmod' => '2024-01-01'
    ],
    'robots_txt' => [
        'allow' => true,
        'allowList' => ['google', 'bing']
    ]
])]
```

Route options take precedence over attributes when both are defined.

### Event System

**AddDynamicRoutesEvent** (src/Event/AddDynamicRoutesEvent.php)
- Allows adding dynamic URLs to sitemap
- Event subscribers call `$event->addUrlToContainer(RouteOptions)`
- See docs/04-dynamic_routes.md for implementation examples

**AddRobotsTxtEvent** (src/Event/AddRobotsTxtEvent.php)
- Allows adding dynamic robots.txt rules

### Console Commands

**svc:sitemap:create_xml** (src/Command/CreateSitemapCommand.php)
- Creates sitemap.xml file
- Options: --path, --file, --gzip
- Uses command locking to prevent concurrent execution

**svc:robots:create_txt** (src/Command/CreateRobotsTxtCommand.php)
- Creates robots.txt file
- Options: --path, --file

## Code Style

- **Strict types:** All PHP files must include `declare(strict_types=1);` after the opening tag
- **Indentation:** 4 spaces (standard PSR-12, note: the .php-cs-fixer.php file itself uses 2 spaces for historical reasons)
- **PHP-CS-Fixer rules:** @Symfony + @PSR12 preset with modifications:
  - declare_strict_types: true (enforced)
  - yoda_style: false
  - concat_space: "one" space around concatenation
  - array_syntax: short (always use `[]` not `array()`)
  - combine_consecutive_unsets: true
  - single_quote: true
  - phpdoc_order: true
  - Array indentation enforced
- **File headers:** All PHP files must include the license header block:
  ```php
  /*
   * This file is part of the SvcSitemap bundle.
   *
   * (c) 2025 Sven Vetter <dev@sv-systems.com>.
   *
   * For the full copyright and license information, please view the LICENSE
   * file that was distributed with this source code.
   */
  ```
  This header is automatically enforced and updated by PHP-CS-Fixer
- **PHPDoc:** Required for all public methods, especially array type hints
- **Line length:** Keep reasonable (typically around 120 chars)

## Testing Structure

- **Unit tests:** tests/Unit/ - Test individual classes in isolation
- **Integration tests:** tests/Integration/ - Test with full Symfony kernel
- **Standards tests:** tests/Standards/ - Enforce code standards (license headers, docblocks)
- **Test kernel:** tests/Integration/SvcSitemapTestingKernel.php - Custom kernel for integration tests

## Configuration

Bundle configuration lives in bundle's config/services.yaml but is configured by users in their app's config/packages/svc_sitemap.yaml. Key settings:

### Sitemap Configuration
- `sitemap.default_values.priority` (float, 0-1) - Default priority for routes
- `sitemap.default_values.change_freq` (enum) - Default change frequency
- `sitemap.translation.enabled` (bool) - Enable multi-language support
- `sitemap.translation.locales` (array) - List of supported locales
- `sitemap.sitemap_directory` (string) - Output directory
- `sitemap.sitemap_filename` (string) - Output filename

### Robots Configuration
- `robots.robots_directory` (string) - Output directory for robots.txt
- `robots.robots_filename` (string) - Output filename
- `robots.sitemap_url` (string, optional) - Full URL to sitemap.xml to include in robots.txt
  - Example: `https://example.com/sitemap.xml`
  - If set, adds `Sitemap: <url>` line to robots.txt
- `robots.translation.enabled` (bool) - Enable translation support for robots.txt

## Exception Handling

### Custom Exceptions
- `SitemapTooLargeException` - Thrown when sitemap exceeds 50,000 URLs or 50MB
- `CannotWriteSitemapXML` - Thrown when file write fails (includes file path and original error)
- `TranslationNotEnabled` - Thrown when translation is required but not enabled
- `RobotsTranslationNotEnabled` - Thrown when robots translation is required but not enabled
- `RobotsFilenameMissing` - Thrown when robots filename is not configured

All exceptions extend `_SitemapException` for easy catching.

## Documentation Structure

The bundle has comprehensive documentation in the `docs/` folder:

### Sitemap Documentation
- `01-installation.md` - Installation and basic setup
- `02-config.md` - Bundle configuration options
- `03-static_routes.md` - Configure static routes via attributes or route options
- `04-dynamic_routes.md` - Add dynamic routes via event subscribers
- `05-dump_sitemap.md` - Generate sitemap.xml via console or controller

### Robots.txt Documentation
- `06-robots_static.md` - Configure static robots.txt rules via route options
- `07-robots_dynamic.md` - Add dynamic robots.txt rules via event subscribers
- `08-dump_robots.md` - Generate robots.txt via console or controller

## Dependencies

- PHP 8+
- Symfony 6.0+ or 7.0+ (framework-bundle, console, yaml)
- PHPUnit 12.4+ (dev)
- PHPStan 2.1+ (dev, level 7)
- CHANGELOG.md wird über den Release-Prozess aktualisiert (siehe bin/release.php), bitte Änderungen dort eintragen