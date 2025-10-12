# Installation

## Requirements

- PHP 8.0 or higher
- Symfony 6.0 or 7.0+

## Installation via Composer

Open a command console, enter your project directory and execute:

```bash
composer require svc/sitemap-bundle
```

### For Applications using Symfony Flex

The bundle will be automatically registered. You're done!

### For Applications not using Symfony Flex

Enable the bundle by adding it to the list of registered bundles in `config/bundles.php`:

```php
// config/bundles.php

return [
    // ...
    Svc\SitemapBundle\SvcSitemapBundle::class => ['all' => true],
];
```

## Quick Start

After installation, you can immediately start using the bundle:

### 1. Configure a route for the sitemap

```php
use Svc\SitemapBundle\Attribute\Sitemap;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/', name: 'homepage')]
#[Sitemap(priority: 1.0, changeFreq: ChangeFreq::DAILY)]
public function home(): Response
{
    //...
}
```

### 2. Generate the sitemap.xml

```bash
bin/console svc:sitemap:create_xml
```

Your sitemap.xml is now available at `public/sitemap.xml`!

## Next Steps

- [Configuration](02-config.md) - Configure default values and translation
- [Static routes](03-static_routes.md) - Learn about route configuration options
- [Dynamic routes](04-dynamic_routes.md) - Add dynamic content via events
- [Dumping sitemap](05-dump_sitemap.md) - Learn about generation options
