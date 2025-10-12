# Dynamic Sitemap Routes

Dynamic routes allow you to add URLs to your sitemap that come from databases, APIs, or other dynamic sources. This is done by subscribing to the `AddDynamicRoutesEvent`.

## When to Use Dynamic Routes

Use dynamic routes when you need to add URLs that:
- Come from database entities (products, blog posts, user profiles, etc.)
- Are generated at runtime
- Depend on external data sources
- Change frequently and can't be defined statically in your routing configuration

## Basic Example

Let's say you have a video application and want to add all videos to your sitemap.

> **Note:** We use an `event subscriber` in this example, but you can also use an `event listener`.

If you are not familiar with the concept of event listener/subscriber/dispatcher,
please have a look to Symfony's [official documentation](http://symfony.com/doc/current/event_dispatcher.html).

## Creating an EventSubscriber

```php
<?php

namespace App\EventSubscriber;

use Svc\SitemapBundle\Entity\RouteOptions;
use Svc\SitemapBundle\Enum\ChangeFreq;
use Svc\SitemapBundle\Event\AddDynamicRoutesEvent;
use App\Repository\VideoRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AddVideosToSitemap implements EventSubscriberInterface
{
    private const ROUTE_NAME = 'app_video_show';

    public function __construct(
        private VideoRepository $videoRepository,
    ) {
    }

    /**
     * Register to the sitemap event
     */
    public static function getSubscribedEvents(): array
    {
        return [
            AddDynamicRoutesEvent::class => 'addDynamicURLs',
        ];
    }

    /**
     * Add video URLs to the sitemap
     */
    public function addDynamicURLs(AddDynamicRoutesEvent $event): void
    {
        // Fetch all published videos
        $videos = $this->videoRepository->findBy(['published' => true]);

        foreach ($videos as $video) {
            // Create a new RouteOptions object with the route name
            $route = new RouteOptions(self::ROUTE_NAME);

            // Set route parameters (used to generate the URL)
            $route->setRouteParam(['id' => $video->getId()]);

            // Set last modification date
            if ($video->getUpdatedAt()) {
                $route->setLastMod($video->getUpdatedAt());
            }

            // Set priority (0.0 to 1.0)
            $route->setPriority(0.7);

            // Set change frequency
            $route->setChangeFreq(ChangeFreq::WEEKLY);

            // Add the URL to the sitemap container
            $event->addUrlToContainer($route);
        }
    }
}
```

> **Important:** With large datasets, `findBy()` can cause memory issues. Use Doctrine's [batch processing](https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/batch-processing.html) for better performance.

## RouteOptions API

The `RouteOptions` class provides these methods:

| Method | Description |
|--------|-------------|
| `setRouteParam(array $params)` | Set route parameters for URL generation |
| `setLastMod(\DateTimeImmutable $date)` | Set last modification date |
| `setPriority(float $priority)` | Set priority (0.0 to 1.0) |
| `setChangeFreq(ChangeFreq $freq)` | Set change frequency enum |
| `setAlternates(array $alternates)` | Set alternate language URLs |

## Advanced Example: Blog Posts with Categories

```php
<?php

namespace App\EventSubscriber;

use Svc\SitemapBundle\Entity\RouteOptions;
use Svc\SitemapBundle\Enum\ChangeFreq;
use Svc\SitemapBundle\Event\AddDynamicRoutesEvent;
use App\Repository\BlogPostRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AddBlogToSitemap implements EventSubscriberInterface
{
    public function __construct(
        private BlogPostRepository $postRepository,
        private CategoryRepository $categoryRepository,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            AddDynamicRoutesEvent::class => 'addDynamicURLs',
        ];
    }

    public function addDynamicURLs(AddDynamicRoutesEvent $event): void
    {
        // Add all blog categories
        $this->addCategories($event);

        // Add all blog posts
        $this->addBlogPosts($event);
    }

    private function addCategories(AddDynamicRoutesEvent $event): void
    {
        $categories = $this->categoryRepository->findAll();

        foreach ($categories as $category) {
            $route = new RouteOptions('app_blog_category');
            $route->setRouteParam(['slug' => $category->getSlug()]);
            $route->setPriority(0.6);
            $route->setChangeFreq(ChangeFreq::WEEKLY);

            $event->addUrlToContainer($route);
        }
    }

    private function addBlogPosts(AddDynamicRoutesEvent $event): void
    {
        $posts = $this->postRepository->findPublished();

        foreach ($posts as $post) {
            $route = new RouteOptions('app_blog_post');
            $route->setRouteParam(['slug' => $post->getSlug()]);
            $route->setLastMod($post->getUpdatedAt());

            // Higher priority for recent posts
            $daysOld = $post->getCreatedAt()->diff(new \DateTime())->days;
            $priority = $daysOld < 7 ? 0.9 : 0.7;
            $route->setPriority($priority);

            $route->setChangeFreq(ChangeFreq::MONTHLY);

            $event->addUrlToContainer($route);
        }
    }
}
```

## Multi-language Support

If you have multi-language routes, you can specify alternate URLs:

```php
public function addDynamicURLs(AddDynamicRoutesEvent $event): void
{
    $products = $this->productRepository->findAll();

    foreach ($products as $product) {
        $route = new RouteOptions('app_product_show');
        $route->setRouteParam(['id' => $product->getId(), '_locale' => 'en']);
        $route->setPriority(0.8);

        // Add alternate language versions
        $route->setAlternates([
            'de' => [
                'route' => 'app_product_show',
                'params' => ['id' => $product->getId(), '_locale' => 'de'],
            ],
            'fr' => [
                'route' => 'app_product_show',
                'params' => ['id' => $product->getId(), '_locale' => 'fr'],
            ],
        ]);

        $event->addUrlToContainer($route);
    }
}
```

## Performance Optimization

### Batch Processing for Large Datasets

Use Doctrine's iterator for memory-efficient processing:

```php
public function addDynamicURLs(AddDynamicRoutesEvent $event): void
{
    $query = $this->productRepository->createQueryBuilder('p')
        ->where('p.published = true')
        ->getQuery();

    // Process in batches to avoid memory issues
    foreach ($query->toIterable() as $product) {
        $route = new RouteOptions('app_product_show');
        $route->setRouteParam(['id' => $product->getId()]);
        $route->setPriority(0.8);

        $event->addUrlToContainer($route);

        // Optional: Clear entity manager every 100 items
        // $this->entityManager->clear();
    }
}
```

### Selective Inclusion

Only include relevant pages in your sitemap:

```php
public function addDynamicURLs(AddDynamicRoutesEvent $event): void
{
    // Only include published and non-archived posts
    $posts = $this->postRepository->findBy([
        'published' => true,
        'archived' => false,
    ]);

    foreach ($posts as $post) {
        // Skip posts older than 2 years
        if ($post->getCreatedAt() < new \DateTime('-2 years')) {
            continue;
        }

        $route = new RouteOptions('app_blog_post');
        $route->setRouteParam(['slug' => $post->getSlug()]);
        $event->addUrlToContainer($route);
    }
}
```

## Testing Your Event Subscriber

Generate the sitemap to verify your URLs are included:

```bash
bin/console svc:sitemap:create_xml
```

Check the generated `public/sitemap.xml` file to ensure all expected URLs are present.

## See Also

- [Static routes](03-static_routes.md) - Configure static routes
- [Dumping sitemap](05-dump_sitemap.md) - Generate sitemap files
- [Configuration](02-config.md) - Configure translation and defaults