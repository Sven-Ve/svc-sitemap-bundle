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

namespace Svc\SitemapBundle\Tests\Unit\Attribute;

use PHPUnit\Framework\TestCase;
use Svc\SitemapBundle\Attribute\Sitemap;
use Svc\SitemapBundle\Enum\ChangeFreq;

/**
 * test the Sitemap attribute.
 */
class SitemapTest extends TestCase
{
    public function testAttributeWithAllParameters(): void
    {
        $attribute = new Sitemap(
            priority: 0.8,
            changeFreq: ChangeFreq::DAILY,
            lastMod: '2024-01-01'
        );

        self::assertSame(0.8, $attribute->priority);
        self::assertSame(ChangeFreq::DAILY, $attribute->changeFreq);
        self::assertSame('2024-01-01', $attribute->lastMod);
        self::assertTrue($attribute->enabled);
    }

    public function testAttributeWithDefaultValues(): void
    {
        $attribute = new Sitemap();

        self::assertNull($attribute->priority);
        self::assertNull($attribute->changeFreq);
        self::assertNull($attribute->lastMod);
        self::assertTrue($attribute->enabled);
    }

    public function testAttributeDisabled(): void
    {
        $attribute = new Sitemap(enabled: false);

        self::assertFalse($attribute->enabled);
    }

    public function testToArrayWithAllParameters(): void
    {
        $attribute = new Sitemap(
            priority: 0.8,
            changeFreq: ChangeFreq::WEEKLY,
            lastMod: '2024-01-01'
        );

        $expected = [
            'priority' => 0.8,
            'changefreq' => ChangeFreq::WEEKLY,
            'lastmod' => '2024-01-01',
        ];

        self::assertEquals($expected, $attribute->toArray());
    }

    public function testToArrayWithPartialParameters(): void
    {
        $attribute = new Sitemap(priority: 0.5);

        $expected = [
            'priority' => 0.5,
        ];

        self::assertEquals($expected, $attribute->toArray());
    }

    public function testToArrayWithNoParameters(): void
    {
        $attribute = new Sitemap();

        self::assertTrue($attribute->toArray());
    }

    public function testToArrayWhenDisabled(): void
    {
        $attribute = new Sitemap(enabled: false);

        self::assertFalse($attribute->toArray());
    }

    public function testInvalidPriorityTooLow(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Priority must be between 0.0 and 1.0, got -0.1');

        new Sitemap(priority: -0.1);
    }

    public function testInvalidPriorityTooHigh(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Priority must be between 0.0 and 1.0, got 1.5');

        new Sitemap(priority: 1.5);
    }

    public function testValidPriorityEdgeCases(): void
    {
        $attr1 = new Sitemap(priority: 0.0);
        $attr2 = new Sitemap(priority: 1.0);

        self::assertSame(0.0, $attr1->priority);
        self::assertSame(1.0, $attr2->priority);
    }
}
