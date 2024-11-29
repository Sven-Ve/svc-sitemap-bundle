<?php

declare(strict_types=1);

namespace Svc\SitemapBundle\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use Svc\SitemapBundle\Entity\RouteOptions;

/**
 * testing the SvcLog entity class.
 */
final class RouteOptionsTest extends TestCase
{
  public function testCreateRouteOptions(): void
  {
    $routeOptions = new RouteOptions('test');
    $this->assertEquals('test', $routeOptions->routeName);
    $this->assertNull($routeOptions->changeFreq);
    $this->assertNull($routeOptions->lastMod);
    $this->assertNull($routeOptions->priority);
  }
}
