<?php

namespace Svc\SitemapBundle\Event;

use Svc\SitemapBundle\Entity\RouteOptions;
use Symfony\Contracts\EventDispatcher\Event;

final class AddDynamicRoutesEvent extends Event
{

  /**
   * @param array<RouteOptions> $urls
   */
  public function __construct(private array $urls) {}


  /**
   * @return array<RouteOptions>
   */
  public function getUrls(): array
  {
    return $this->urls;
  }

  /**
   * @param array<RouteOptions> $urls
   */
  public function setUrls(array $urls): self
  {
    $this->urls = $urls;

    return $this;
  }

  /**
   * Set the value of urls
   */
  public function addUrl(RouteOptions $url): self
  {
    $this->urls[] = $url;

    return $this;
  }
}
