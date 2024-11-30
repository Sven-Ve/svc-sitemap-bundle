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
    $this->assertEquals('test', $routeOptions->getRouteName());
    $this->assertNull($routeOptions->getChangeFreq());
    $this->assertNull($routeOptions->getChangeFreqText());
    $this->assertNull($routeOptions->getLastMod());
    $this->assertNull($routeOptions->getPriority());
    $this->assertNull($routeOptions->getUrl());
  }
}
