# Dynamic robots.txt rules

Just like with sitemap.xml, you can add dynamic robots.txt rules via event subscribers or listeners. This is useful when you need to generate robots.txt rules based on database content or other dynamic sources.

## EventSubscriber Example

Here's an example that adds robots.txt rules for a video application, allowing only Google and Bing to access video pages:

```php
<?php

namespace App\EventSubscriber;

use Svc\SitemapBundle\Event\AddRobotsTxtEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AddVideosToRobotsTxt implements EventSubscriberInterface
{
    /**
     * Register to the robots.txt event
     */
    public static function getSubscribedEvents(): array
    {
        return [
            AddRobotsTxtEvent::class => 'addDynamicRules',
        ];
    }

    /**
     * Add dynamic robots.txt rules
     */
    public function addDynamicRules(AddRobotsTxtEvent $event): void
    {
        // Allow Google and Bing to access all videos
        $event->addAllowUserAgents('/videos/*', ['google', 'bing']);

        // Disallow all other bots from accessing video uploads
        $event->addDisallowUserAgents('/videos/upload', ['*']);
    }
}
```

> **Note:** We choose an `event subscriber` as example, but you can also do it with an `event listener`.

If you are not familiar with the concept of event listener/subscriber/dispatcher,
please have a look to Symfony's [official documentation](http://symfony.com/doc/current/event_dispatcher.html).

## AddRobotsTxtEvent API

The `AddRobotsTxtEvent` provides two convenience methods:

### addAllowUserAgents()

Adds an "Allow" directive for specific user agents:

```php
$event->addAllowUserAgents(string $path, array $userAgents): void
```

**Parameters:**
- `$path` - The path pattern (e.g., `/api/*`, `/public`)
- `$userAgents` - Array of user agent names (e.g., `['google', 'bing']`)

**Example:**

```php
// Allow Google and Bing to access the API
$event->addAllowUserAgents('/api/*', ['google', 'bing']);
```

### addDisallowUserAgents()

Adds a "Disallow" directive for specific user agents:

```php
$event->addDisallowUserAgents(string $path, array $userAgents): void
```

**Parameters:**
- `$path` - The path pattern (e.g., `/admin/*`, `/private`)
- `$userAgents` - Array of user agent names (e.g., `['*']` for all bots)

**Example:**

```php
// Disallow all bots from accessing admin area
$event->addDisallowUserAgents('/admin/*', ['*']);
```

## Advanced Example with Database

Here's a more advanced example that reads from a database to determine which paths should be blocked:

```php
<?php

namespace App\EventSubscriber;

use App\Repository\PageRepository;
use Svc\SitemapBundle\Event\AddRobotsTxtEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DynamicRobotsTxtSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private PageRepository $pageRepository,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            AddRobotsTxtEvent::class => 'addDynamicRules',
        ];
    }

    public function addDynamicRules(AddRobotsTxtEvent $event): void
    {
        // Allow Google and Bing to access public pages
        $publicPages = $this->pageRepository->findBy(['public' => true]);
        foreach ($publicPages as $page) {
            $event->addAllowUserAgents($page->getPath(), ['google', 'bing']);
        }

        // Disallow all bots from accessing private pages
        $privatePages = $this->pageRepository->findBy(['public' => false]);
        foreach ($privatePages as $page) {
            $event->addDisallowUserAgents($page->getPath(), ['*']);
        }

        // Special rule: Allow only Google to access beta features
        $betaPages = $this->pageRepository->findBy(['beta' => true]);
        foreach ($betaPages as $page) {
            $event->addAllowUserAgents($page->getPath(), ['google']);
        }
    }
}
```

> **Note:** With large datasets, `findBy` without proper filtering can be slow.
> Please read Doctrine documentation about iterator and array hydration for better performance.

## Using RobotsOptions Directly

For more control, you can also work with `RobotsOptions` objects directly:

```php
<?php

namespace App\EventSubscriber;

use Svc\SitemapBundle\Entity\RobotsOptions;
use Svc\SitemapBundle\Event\AddRobotsTxtEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CustomRobotsTxtSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            AddRobotsTxtEvent::class => 'addCustomRules',
        ];
    }

    public function addCustomRules(AddRobotsTxtEvent $event): void
    {
        // Get the robots container
        $container = $event->getRobotsContainer();

        // Create a custom robots option
        $robotsOption = new RobotsOptions('custom_route');
        $robotsOption->setPath('/custom/path/*');
        $robotsOption->setAllow(true);
        $robotsOption->setAllowList(['google', 'bing', 'duckduckbot']);

        // Add it to the container
        $container[] = $robotsOption;
    }
}
```

## Example Output

The following event subscriber:

```php
public function addDynamicRules(AddRobotsTxtEvent $event): void
{
    $event->addAllowUserAgents('/api/public', ['google', 'bing']);
    $event->addDisallowUserAgents('/api/private', ['*']);
}
```

Will generate this robots.txt (combined with static routes):

```
User-agent: google
Allow: /
Allow: /api/public

User-agent: bing
Allow: /
Allow: /api/public

User-agent: *
Disallow: /api/private

```

## See Also

- [Static robots.txt configuration](06-robots_static.md) - Configure robots.txt via route options
- [Dumping robots.txt](08-dump_robots.md) - How to generate the robots.txt file
- [Configuration](02-config.md) - Bundle configuration options
