<?php

/*
 * This file is part of the SvcSitemapBundle package.
 *
 * (c) Sven Vetter <https://github.com/Sven-Ve/svc-sitemap-bundle>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Svc\SitemapBundle\Service;

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

/**
 * common route functions.
 */
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
