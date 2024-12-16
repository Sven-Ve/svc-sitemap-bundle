<?php

/*
 * This file is part of the SvcSitemapBundle package.
 *
 * (c) Sven Vetter <https://github.com/Sven-Ve/svc-sitemap-bundle>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Svc\SitemapBundle\Tests\Integration\Sitemap;

use Svc\SitemapBundle\Entity\RouteOptions;
use Svc\SitemapBundle\Enum\ChangeFreq;
use Svc\SitemapBundle\Sitemap\SitemapHelper;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * testing the sitemap helper class.
 */
class SiteMapHelperTest extends KernelTestCase
{
  public function testLoadRoutes(): void
  {
    $kernel = self::bootKernel();
    $container = $kernel->getContainer();
    $sitemapHelper = $container->get('Svc\SitemapBundle\Sitemap\SitemapHelper');

    $this->assertInstanceOf(SitemapHelper::class, $sitemapHelper);

    $routes = $sitemapHelper->findStaticRoutes();
    $this->assertEquals(1, count($routes));

    $route = $routes[0];
    $this->assertInstanceOf(RouteOptions::class, $route);
    $this->assertEquals('svc_sitemap_test', $route->getRouteName());
    $this->assertEquals(0.2, $route->getPriority());
  }

  public function testNormalizeRoutes(): void
  {
    $kernel = self::bootKernel();
    $container = $kernel->getContainer();

    $sitemapHelper = $container->get('Svc\SitemapBundle\Sitemap\SitemapHelper');
    $this->assertInstanceOf(SitemapHelper::class, $sitemapHelper);

    $routes = $sitemapHelper->findStaticRoutes();
    $routes = $sitemapHelper->normalizeRoutes($routes);

    $route = $routes[0];
    $this->assertEquals('http://localhost/test/sitemap/', $route->getUrl());
    $this->assertEquals(0.2, $route->getPriority());
    $this->assertEquals(ChangeFreq::WEEKLY, $route->getChangeFreq());
    $this->assertEquals('weekly', $route->getChangeFreqText());
    $this->assertEquals(new \DateTimeImmutable('2024-12-09'), $route->getLastMod());
  }

  protected static function ensureKernelShutdown(): void
  {
    $wasBooted = static::$booted;
    parent::ensureKernelShutdown();

    if ($wasBooted) {
      restore_exception_handler();
    }
  }
}
