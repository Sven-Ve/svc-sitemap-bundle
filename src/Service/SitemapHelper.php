<?php

namespace Svc\SitemapBundle\Service;

use Svc\SitemapBundle\Entity\RouteOptions;
use Svc\SitemapBundle\Enum\ChangeFreq;
use Symfony\Component\Routing\RouterInterface;

class SitemapHelper
{
  public function __construct(private RouterInterface $router) {}

  public function create(): string
  {
    $staticRoutes = $this->findStaticRoutes();
    return CreateXML::create($this->normalizeRoutes($staticRoutes));
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
   * fill url, fill empty fields with default values
   *
   * @param array<RouteOptions> $routes
   * @return array<RouteOptions>
   */
  private function normalizeRoutes(array $routes): array
  {
    foreach ($routes as $route) {
      $route->url = $this->router->generate($route->routeName, [], RouterInterface::ABSOLUTE_URL);

      if (!$route->lastMod) {
        $route->lastMod = new \DateTimeImmutable('now');
      }

      if (!$route->changeFreq) {
        $route->changeFreq = ChangeFreq::WEEKLY;
      }

      if ($route->priority === null) {
        $route->priority = 0.5;
      }

    }
    return $routes;
  }
}
