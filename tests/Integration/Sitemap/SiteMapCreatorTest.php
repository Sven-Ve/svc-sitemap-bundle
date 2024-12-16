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

use Svc\SitemapBundle\Sitemap\SitemapCreator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * testing the SiteMapCreator class.
 */
class SiteMapCreatorTest extends KernelTestCase
{
  public function testLoadSiteMapCreator(): void
  {
    $kernel = self::bootKernel();
    $container = $kernel->getContainer();
    $sitemapCreator = $container->get('Svc\SitemapBundle\Sitemap\SitemapCreator');

    $this->assertInstanceOf(SitemapCreator::class, $sitemapCreator);
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
