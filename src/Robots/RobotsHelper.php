<?php

namespace Svc\SitemapBundle\Robots;

use Svc\SitemapBundle\Entity\RobotsOptions;
use Svc\SitemapBundle\Service\RouteHandler;
use Symfony\Component\Routing\RouteCollection;

final class RobotsHelper
{
  private RouteCollection $routeCollection;

  public function __construct(
    private RouteHandler $routeHandler,
  ) {
  }

  /**
   * @return RobotsOptions[]
   */
  public function findStaticRoutes(): array
  {
    $allRoutes = [];
    $this->routeCollection = $this->routeHandler->getRouteCollection();

    foreach ($this->routeCollection->all() as $name => $route) {
      $robotsOptions = RobotsRouteParser::parse($name, $route);

      if (!$robotsOptions) {
        continue;
      }

      $allRoutes[] = $robotsOptions;
    }

    $definitions = $this->createRobotsArray($allRoutes);
    dd($definitions);

    //return $allRoutes;
  }

  /**
   * @param RobotsOptions[] $allRoutes
   * @return array<mixed>
   */
  private function createRobotsArray(array $allRoutes): array
  {
    $definitions = [];

    foreach ($allRoutes as $route) {
      if ($route->getAllow()) {
        foreach ($route->getAllowList() as $userAgent) {
          $definitions[$userAgent]['allow'][$route->getPath()] = 1;
        }
      }

      if ($route->getDisAllow()) {
        foreach ($route->getDisAllowList() as $userAgent) {
          $definitions[$userAgent]['disallow'][$route->getPath()] = 1;
        }
      }
    }

    return $definitions;
  }
}
