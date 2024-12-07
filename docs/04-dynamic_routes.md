# Dynamic routes

You can also register event listeners (or subscribers) to populate your sitemap(s).

Imagine that your application manage videos, and that you want to add to your sitemap all videos.

> **Note:** We choose an `event subscriber` as example, but you can also do it with an `event listener`.

If you are not familiar with the concept of event listener/subscriber/dispatcher, 
please have a look to Symfony's [official documentation](http://symfony.com/doc/current/event_dispatcher.html).


## EventListener class

```php
namespace App\EventSubscriber;

use Svc\SitemapBundle\Entity\RouteOptions;
use Svc\SitemapBundle\Event\AddDynamicRoutesEvent;
use Svc\VideoBundle\Repository\VideoRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AddVideosToSitemap implements EventSubscriberInterface
{
  public const ROUTE_NAME = 'svc_video_run';

  public function __construct(
    private VideoRepository $videoRep,
  ) {
  }

  /**
   * register to the sitemap-event
   */
  public static function getSubscribedEvents(): array
  {
    return [
      AddDynamicRoutesEvent::class => 'addDynamicURLs',
    ];
  }

  /**
   * load my routes to the sitemap collection
   */
  public function addDynamicURLs(AddDynamicRoutesEvent $event)
  {
    foreach ($this->videoRep->findBy([]) as $video) {

      # create a new entity object with the route name
      $route = new RouteOptions(self::ROUTE_NAME);

      # (optional) set parameter
      $route->setRouteParam(['id' => $video->getIDorShortname()]);

      # (optional) set last modify date
      if ($video->getUploadDate()) {
        $date = \DateTime::createFromInterface($video->getUploadDate());
        $route->setLastMod(\DateTimeImmutable::createFromMutable($date));
      }

      # push data
      $event->addUrlToContainer($route);
    }
  }
}
```

> **Note:** you should not use this snippet as is. With large dataset, `findBy` without filter is not a good idea. 
> Please read Doctrine documentation, to learn about iterator and array hydrate.