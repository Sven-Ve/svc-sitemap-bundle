<?php

declare(strict_types=1);

/*
 * This file is part of the SvcSitemap bundle.
 *
 * (c) 2025 Sven Vetter <dev@sv-systems.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Svc\SitemapBundle\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use Svc\SitemapBundle\Entity\RouteOptions;
use Svc\SitemapBundle\Enum\ChangeFreq;

/**
 * test the route options entity (for sitemap.xml).
 */
final class RouteOptionsTest extends TestCase
{
    public const TEST_URL = 'https://www.test.com';

    /**
     * testing the RouteOptions entity class (empty).
     */
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

    /**
     * testing the RouteOptions entity class (filled).
     */
    public function testCreateAndFillRouteOptions(): void
    {
        $routeOptions = new RouteOptions('test');
        $this->assertEquals('test', $routeOptions->getRouteName());

        $routeOptions->setChangeFreq(ChangeFreq::ALWAYS);
        $this->assertEquals(ChangeFreq::ALWAYS, $routeOptions->getChangeFreq());
        $this->assertEquals('always', $routeOptions->getChangeFreqText());

        $currentDate = new \DateTimeImmutable('now');
        $routeOptions->setLastMod($currentDate);
        $this->assertEquals($currentDate, $routeOptions->getLastMod());
        $this->assertEquals($currentDate->format('c'), $routeOptions->getLastModXMLFormat());

        $routeOptions->setPriority(0.1);
        $this->assertEquals(0.1, $routeOptions->getPriority());

        $routeOptions->setUrl(self::TEST_URL);
        $this->assertEquals(self::TEST_URL, $routeOptions->getUrl());
    }
}
