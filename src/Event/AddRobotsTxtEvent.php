<?php

namespace Svc\SitemapBundle\Event;

use Svc\SitemapBundle\Entity\RobotsOptions;
use Symfony\Contracts\EventDispatcher\Event;

final class AddRobotsTxtEvent extends Event
{
  /**
   * @param array<RobotsOptions> $robotsContainer
   */
  public function __construct(
    private array $robotsContainer,
  ) {
  }

  /**
   * @return array<RobotsOptions>
   */
  public function getRobotsContainer(): array
  {
    return $this->robotsContainer;
  }

  /**
   * add allow definition for a path to robots.txt.
   *
   * @param array<string> $userAgents
   */
  public function addAllowUserAgents(string $path, array $userAgents): void
  {
    $definition = new RobotsOptions($path);
    $definition->setAllow(true);
    $definition->setAllowList($userAgents);
    $definition->setPath($path);
    $this->robotsContainer[] = $definition;
  }

  /**
   * add disallow definition for a path to robots.txt.
   *
   * @param array<string> $userAgents
   */
  public function addDisallowUserAgents(string $path, array $userAgents): void
  {
    $definition = new RobotsOptions($path);
    $definition->setDisallow(true);
    $definition->setDisallowList($userAgents);
    $definition->setPath($path);
    $this->robotsContainer[] = $definition;
  }
}
