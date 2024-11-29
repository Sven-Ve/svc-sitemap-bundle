<?php

namespace Svc\SitemapBundle\Entity;

use Svc\SitemapBundle\Enum\ChangeFreq;

class RouteOptions
{
  public function __construct(public string $routeName)
  {
  }

  public ?\DateTimeImmutable $lastMod = null;

  public ?int $priority = null;

  public ?ChangeFreq $changeFreq = null;
}
