<?php

namespace Svc\SitemapBundle\Service;

use Symfony\Component\Routing\RouterInterface;

class SitemapHelper
{
  public function __construct(private RouterInterface $router) {}

  public function create(): void
  {
    $staticRoutes = $this->findStaticRoutes();
    dd($staticRoutes);
    dd("im helper");
  }


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
    dump($allRoutes);

    dd("fertig");
  }
}
