# Static robots.txt configuration

Static robots.txt rules can be configured directly on your routes, similar to sitemap configuration. This allows you to define which user agents (search engines) can or cannot access specific routes.

There are two ways to configure static robots.txt rules:

1. **Using the `#[Robots]` attribute** (recommended, PHP 8+ only)
2. **Using route options** (works with all PHP versions)

## Method 1: Using the `#[Robots]` Attribute (Recommended)

The `#[Robots]` attribute provides a clean, type-safe way to configure robots.txt settings directly on controller methods:

```php
<?php

namespace App\Controller;

use Svc\SitemapBundle\Attribute\Robots;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DefaultController extends AbstractController
{
    // Allow Google and Bing to access the homepage
    #[Route('/', name: 'homepage')]
    #[Robots(allow: true, userAgents: ['google', 'bing'])]
    public function home(): Response
    {
        //...
    }

    // Disallow all bots from accessing admin area
    #[Route('/admin', name: 'admin')]
    #[Robots(allow: false, userAgents: ['*'])]
    public function adminAction(): Response
    {
        //...
    }

    // Allow only Google
    #[Route('/api', name: 'api')]
    #[Robots(allow: true, userAgents: ['google'])]
    public function apiAction(): Response
    {
        //...
    }

    // Explicitly exclude from robots.txt
    #[Route('/private', name: 'private')]
    #[Robots(enabled: false)]
    public function privateAction(): Response
    {
        //...
    }

    // Allow all bots (default behavior)
    #[Route('/public', name: 'public')]
    #[Robots]
    public function publicAction(): Response
    {
        //...
    }
}
```

## Method 2: Using Route Options

> **⚠️ DEPRECATED:** Route options for robots.txt configuration are deprecated since version 1.2 and will be removed in version 2.0. Please use the `#[Robots]` attribute instead.

Alternatively, you can configure robots.txt settings using route options. This method works with all PHP versions:

```php
<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

class DefaultController extends AbstractController
{
    // Allow google and bing to access the homepage
    #[Route('/', name: 'homepage', options: [
        'robots' => [
            'user-agents' => ['google', 'bing'],
            'allow' => true
        ]
    ])]
    public function home()
    {
        //...
    }

    // Disallow all bots from accessing admin area
    #[Route('/admin', name: 'admin', options: [
        'robots' => [
            'user-agents' => ['*'],
            'allow' => false
        ]
    ])]
    public function adminAction()
    {
        //...
    }

    // Allow only google, disallow others
    #[Route('/api', name: 'api', options: [
        'robots' => [
            'user-agents' => ['google'],
            'allow' => true
        ]
    ])]
    public function apiAction()
    {
        //...
    }
}
```

## Configuration Parameters

### For `#[Robots]` Attribute

| Parameter | Type | Description |
|-----------|------|-------------|
| `allow` | `bool` | `true` for Allow directive, `false` for Disallow directive (default: `true`) |
| `userAgents` | `array<string>\|null` | List of user agents this rule applies to (default: `['*']` if not specified) |
| `enabled` | `bool` | Whether to include this route in robots.txt (default: `true`) |

### For Route Options

The `robots_txt` option accepts the following parameters:

| Parameter | Type | Description |
|-----------|------|-------------|
| `user-agents` | `array<string>` | List of user agents (search engine bots) this rule applies to. Use `['*']` for all bots. |
| `allow` | `bool` | `true` to allow access (Allow directive), `false` to disallow access (Disallow directive) |

## Common User Agents

Here are some common user agent names:

- `*` - All bots (wildcard)
- `google` - Google bot
- `googlebot` - Google bot (alternative name)
- `bing` - Microsoft Bing bot
- `bingbot` - Microsoft Bing bot (alternative name)
- `yahoo` - Yahoo bot
- `duckduckbot` - DuckDuckGo bot
- `baiduspider` - Baidu bot
- `yandex` - Yandex bot

## Multi-language Support

If you have multi-language routes using `{_locale}` placeholder, the bundle automatically expands them for all configured locales:

```php
#[Route('/{_locale}/admin', name: 'admin_localized', options: [
    'robots' => [
        'user-agents' => ['*'],
        'allow' => false
    ]
])]
public function adminLocalizedAction()
{
    //...
}
```

This will generate (assuming locales `en` and `de`):

```
User-agent: *
Disallow: /en/admin
Disallow: /de/admin
```

> **Note:** Translation must be enabled in the bundle configuration for this to work. See [Configuration](02-config.md) for details.

## Example Output

The following route configuration:

```php
#[Route('/public', name: 'public', options: [
    'robots' => ['user-agents' => ['google', 'bing'], 'allow' => true]
])]

#[Route('/admin', name: 'admin', options: [
    'robots' => ['user-agents' => ['*'], 'allow' => false]
])]

#[Route('/api', name: 'api', options: [
    'robots' => ['user-agents' => ['google'], 'allow' => true]
])]
```

Will generate this robots.txt:

```
User-agent: google
Allow: /public
Allow: /api

User-agent: bing
Allow: /public

User-agent: *
Disallow: /admin

```

## Precedence

If both a `#[Robots]` attribute and route options are defined on the same route, the **route options take precedence** over the attribute configuration.

## Best Practices

1. **Be specific:** Use specific user agents when possible instead of `*` for better control
2. **Test thoroughly:** Always test your robots.txt to ensure bots have the correct access
3. **Order matters:** More specific rules should come before general rules
4. **Combine with sitemap:** Use the `robots.sitemap_url` configuration to reference your sitemap.xml
5. **Don't rely on security:** robots.txt is a suggestion, not a security mechanism. Use proper authentication for sensitive areas.

## See Also

- [Dynamic robots.txt rules](07-robots_dynamic.md) - Add robots.txt rules dynamically via events
- [Dumping robots.txt](08-dump_robots.md) - How to generate the robots.txt file
- [Configuration](02-config.md) - Bundle configuration options
