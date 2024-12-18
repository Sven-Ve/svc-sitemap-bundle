<?php

/*
 * This file is part of the SvcSitemapBundle package.
 *
 * (c) Sven Vetter <https://github.com/Sven-Ve/svc-sitemap-bundle>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Svc\SitemapBundle\Tests\Unit\Sitemap;

use PHPUnit\Framework\TestCase;
use Svc\SitemapBundle\Entity\RouteOptions;
use Svc\SitemapBundle\Enum\ChangeFreq;
use Svc\SitemapBundle\Sitemap\CreateXML;

/**
 * test the xml generation (for sitemap.xml).
 */
final class CreateXMLTest extends TestCase
{
  public const TEST_URL = 'https://www.test.com';

  public function testCreateXML(): void
  {
    $route = new RouteOptions('test');
    $route->setUrl(self::TEST_URL);
    $route->setChangeFreq(ChangeFreq::ALWAYS);
    $currentDate = new \DateTimeImmutable('2024-12-09');
    $route->setLastMod($currentDate);

    $result = '<?xml version="1.0" encoding="utf-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
  <url>
    <loc>https://www.test.com</loc>
    <lastmod>2024-12-09T00:00:00+00:00</lastmod>
    <changefreq>always</changefreq>
  </url>
</urlset>
';

    $xml = CreateXML::create([$route], false);
    $this->assertEqualsIgnoringCase($result, $xml);
  }
}
