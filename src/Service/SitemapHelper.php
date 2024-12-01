<?php

namespace Svc\SitemapBundle\Service;

use Svc\SitemapBundle\Entity\RouteOptions;
use Svc\SitemapBundle\Enum\ChangeFreq;
use Svc\SitemapBundle\Event\AddDynamicRoutesEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\RouterInterface;

final class SitemapHelper
{
  public function __construct(
    private RouterInterface $router,
    private EventDispatcherInterface $eventDispatcher,
    private ChangeFreq $defaultChangeFreq,
    private float $defaultPriority,
  ) {}

  public function create(): string|bool
  {
    $staticRoutes = $this->findStaticRoutes();

    // $urls = [];
    $event = new AddDynamicRoutesEvent($staticRoutes);
    $this->eventDispatcher->dispatch($event);


    //    dd($event->getUrls());
    return CreateXML::create($this->normalizeRoutes($event->getUrls()));
  }

  /**
   * @return RouteOptions[]
   */
  private function findStaticRoutes(): array
  {
    $collection = $this->router->getRouteCollection();
    $allRoutes = [];

    foreach ($collection->all() as $name => $route) {
      $routeOptions = RouteParser::parse($name, $route);
      if (!$routeOptions) {
        continue;
      }
      $allRoutes[] = $routeOptions;
    }

    return $allRoutes;
  }

  /**
   * fill url, fill empty fields with default values.
   *
   * @param array<RouteOptions> $routes
   *
   * @return array<RouteOptions>
   */
  private function normalizeRoutes(array $routes): array
  {
    foreach ($routes as $route) {
      if (!$route->getUrl()) {
        $route->setUrl($this->router->generate($route->getRouteName(), [], RouterInterface::ABSOLUTE_URL));
      }

      if (!$route->getLastMod()) {
        $route->setLastMod(new \DateTimeImmutable('now'));
      }

      if (!$route->getChangeFreq()) {
        $route->setChangeFreq($this->defaultChangeFreq);
      }

      if ($route->getPriority() === null) {
        $route->setPriority($this->defaultPriority);
      }
    }

    return $routes;
  }
}
