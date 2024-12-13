<?php

namespace Svc\SitemapBundle\Robots;

final class RobotsCreator
{
  public function __construct(
    private RobotsHelper $robotsHelper,
  ) {
  }

  /**
   * @return array<mixed>
   */
  public function create(): array
  {
    $staticRoutes = $this->robotsHelper->findStaticRoutes();
    dd($staticRoutes);
  }
}
