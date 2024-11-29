<?php

namespace Svc\SitemapBundle\Entity;

use DateTimeImmutable;
use Svc\SitemapBundle\Enum\ChangeFreq;

class RouteOptions
{
  public function __construct(public string $routeName)
  {
  }

  public DateTimeImmutable $lastMod;
  public int $priority;
  public ChangeFreq $changeFreq;

}
