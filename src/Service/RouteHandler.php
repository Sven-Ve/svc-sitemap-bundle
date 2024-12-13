<?php

namespace Svc\SitemapBundle\Service;

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

final class RouteHandler
{
  private RouteCollection $routeCollection;

  private bool $areRoutesInitialized = false;

  public function __construct(
    private RouterInterface $router,
  ) {
  }

  public function getRouteCollection(): RouteCollection
  {
    if (!$this->areRoutesInitialized) {
      $this->routeCollection = $this->router->getRouteCollection();
      $this->areRoutesInitialized = true;
    }

    return $this->routeCollection;
  }
}
