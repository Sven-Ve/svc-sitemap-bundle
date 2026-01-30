<?php

declare(strict_types=1);

/*
 * This file is part of the SvcSitemap bundle.
 *
 * (c) 2026 Sven Vetter <dev@sv-systems.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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

    public function testCreateXMLWithInvalidURLThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid URL format');

        $route = new RouteOptions('test');
        $route->setUrl('not-a-valid-url');
        $route->setChangeFreq(ChangeFreq::ALWAYS);
        $route->setLastMod(new \DateTimeImmutable());

        CreateXML::create([$route], false);
    }

    public function testCreateXMLWithJavascriptSchemeThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid URL format');

        $route = new RouteOptions('test');
        $route->setUrl('javascript:alert("xss")');
        $route->setChangeFreq(ChangeFreq::ALWAYS);
        $route->setLastMod(new \DateTimeImmutable());

        CreateXML::create([$route], false);
    }

    public function testCreateXMLWithDataSchemeThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid URL format');

        $route = new RouteOptions('test');
        $route->setUrl('data:text/html,<script>alert("xss")</script>');
        $route->setChangeFreq(ChangeFreq::ALWAYS);
        $route->setLastMod(new \DateTimeImmutable());

        CreateXML::create([$route], false);
    }

    public function testCreateXMLWithNullURLThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('has no URL set');

        $route = new RouteOptions('test');
        // URL nicht setzen
        $route->setChangeFreq(ChangeFreq::ALWAYS);
        $route->setLastMod(new \DateTimeImmutable());

        CreateXML::create([$route], false);
    }

    public function testCreateXMLWithInvalidAlternateURLThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid URL format');

        $route = new RouteOptions('test');
        $route->setUrl(self::TEST_URL);
        $route->setChangeFreq(ChangeFreq::ALWAYS);
        $route->setLastMod(new \DateTimeImmutable());
        $route->addAlternate('de', 'invalid-url');

        CreateXML::create([$route], true);
    }

    public function testCreateXMLWithSpecialCharactersInURL(): void
    {
        $route = new RouteOptions('test');
        // URL mit & (wird automatisch von DOMDocument escaped)
        $route->setUrl('https://www.test.com/search?q=foo&bar=baz');
        $route->setChangeFreq(ChangeFreq::ALWAYS);
        $route->setLastMod(new \DateTimeImmutable('2024-12-09'));

        $xml = CreateXML::create([$route], false);

        // Prüfe dass URL korrekt escaped wurde
        $this->assertStringContainsString('q=foo&amp;bar=baz', $xml);
    }
}
