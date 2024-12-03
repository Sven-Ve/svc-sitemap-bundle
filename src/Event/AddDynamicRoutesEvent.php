<?php

namespace Svc\SitemapBundle\Event;

use Svc\SitemapBundle\Entity\RouteOptions;
use Symfony\Contracts\EventDispatcher\Event;

final class AddDynamicRoutesEvent extends Event
{
  /**
   * @param array<RouteOptions> $urlContainer
   */
  public function __construct(
    private array $urlContainer,
  ) {
  }

  /**
   * @return array<RouteOptions>
   */
  public function getUrlContainer(): array
  {
    return $this->urlContainer;
  }

  /**
   * add a new URL to the container.
   */
  public function addUrlToContainer(RouteOptions $url): self
  {
    $this->urlContainer[] = $url;

    return $this;
  }
}
