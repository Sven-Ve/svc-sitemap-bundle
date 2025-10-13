# Static routes

There are two ways to configure static routes for sitemap generation:

1. **Using the `#[Sitemap]` attribute** (recommended, PHP 8+ only)
2. **Using route options** (works with all PHP versions)

The supported sitemap parameters are:

 * `"lastmod"`: a valid datetime as string (default: `"now"`)
 * `"changefreq"`: change frequency of your resource,
 one of the enums `Svc\SitemapBundle\Enum\ChangeFreq::`, (default: `Svc\SitemapBundle\Enum\ChangeFreq::WEEKLY`)
 * `"priority"`: a number between `0` and `1` (default: `0.5`)

> **Note** you can change defaults in the bundle configuration, see  [here](2-config.md) for more information.

## Method 1: Using the `#[Sitemap]` Attribute (Recommended)

The `#[Sitemap]` attribute provides a clean, type-safe way to configure sitemap settings directly on controller methods:

```php
<?php

namespace App\Controller;

use Svc\SitemapBundle\Attribute\Sitemap;
use Svc\SitemapBundle\Enum\ChangeFreq;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DefaultController extends AbstractController
{
    // Simple usage - include in sitemap with default settings
    #[Route('/', name: 'homepage')]
    #[Sitemap]
    public function home(): Response
    {
        //...
    }

    // Configure priority
    #[Route('/faq', name: 'faq')]
    #[Sitemap(priority: 0.7)]
    public function faqAction(): Response
    {
        //...
    }

    // Configure priority and change frequency
    #[Route('/about', name: 'about')]
    #[Sitemap(priority: 0.7, changeFreq: ChangeFreq::DAILY)]
    public function aboutAction(): Response
    {
        //...
    }

    // Full configuration with lastmod
    #[Route('/news', name: 'news')]
    #[Sitemap(priority: 0.9, changeFreq: ChangeFreq::HOURLY, lastMod: '2024-01-01')]
    public function newsAction(): Response
    {
        //...
    }

    // Explicitly exclude from sitemap
    #[Route('/admin', name: 'admin')]
    #[Sitemap(enabled: false)]
    public function adminAction(): Response
    {
        //...
    }
}
```

## Method 2: Using Route Options

> **⚠️ DEPRECATED:** Route options for sitemap configuration are deprecated since version 1.2 and will be removed in version 2.0. Please use the `#[Sitemap]` attribute instead.

Alternatively, you can configure sitemap settings using route options. This method works with all PHP versions:

```php
<?php

namespace App\Controller;

use Svc\SitemapBundle\Enum\ChangeFreq;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'homepage', options: ['sitemap' => true])]
    public function home()
    {
        //...
    }

    #[Route('/faq', name: 'faq', options: ['sitemap' => ['priority' => 0.7]])]
    public function faqAction()
    {
        //...
    }

    #[Route('/about', name: 'about', options: ['sitemap' => ['priority' => 0.7, 'changefreq' => ChangeFreq::DAILY]])]
    public function aboutAction()
    {
        //...
    }
}
```

## Precedence

If both a `#[Sitemap]` attribute and route options are defined on the same route, the **route options take precedence** over the attribute configuration.
