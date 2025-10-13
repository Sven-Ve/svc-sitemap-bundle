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
use Svc\SitemapBundle\Attribute\Robots;

/**
 * test the Robots attribute.
 */
class RobotsTest extends TestCase
{
    public function testAttributeWithAllowAndUserAgents(): void
    {
        $attribute = new Robots(
            allow: true,
            userAgents: ['google', 'bing']
        );

        self::assertTrue($attribute->allow);
        self::assertSame(['google', 'bing'], $attribute->userAgents);
        self::assertTrue($attribute->enabled);
    }

    public function testAttributeWithDisallow(): void
    {
        $attribute = new Robots(
            allow: false,
            userAgents: ['*']
        );

        self::assertFalse($attribute->allow);
        self::assertSame(['*'], $attribute->userAgents);
        self::assertTrue($attribute->enabled);
    }

    public function testAttributeWithDefaultValues(): void
    {
        $attribute = new Robots();

        self::assertTrue($attribute->allow);
        self::assertNull($attribute->userAgents);
        self::assertTrue($attribute->enabled);
    }

    public function testAttributeDisabled(): void
    {
        $attribute = new Robots(enabled: false);

        self::assertFalse($attribute->enabled);
    }

    public function testToArrayWithAllow(): void
    {
        $attribute = new Robots(
            allow: true,
            userAgents: ['google', 'bing']
        );

        $expected = [
            'allow' => true,
            'allowList' => ['google', 'bing'],
        ];

        self::assertEquals($expected, $attribute->toArray());
    }

    public function testToArrayWithDisallow(): void
    {
        $attribute = new Robots(
            allow: false,
            userAgents: ['*']
        );

        $expected = [
            'disallow' => true,
            'disallowList' => ['*'],
        ];

        self::assertEquals($expected, $attribute->toArray());
    }

    public function testToArrayWithDefaultUserAgents(): void
    {
        $attribute = new Robots(allow: false);

        $expected = [
            'disallow' => true,
            'disallowList' => ['*'],
        ];

        self::assertEquals($expected, $attribute->toArray());
    }

    public function testToArrayWhenDisabled(): void
    {
        $attribute = new Robots(enabled: false);

        self::assertFalse($attribute->toArray());
    }

    public function testInvalidUserAgentType(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('User agent must be a string, got integer');

        /* @phpstan-ignore-next-line - Testing invalid type on purpose */
        new Robots(userAgents: ['google', 123]);
    }

    public function testAllowWithMultipleUserAgents(): void
    {
        $attribute = new Robots(
            allow: true,
            userAgents: ['google', 'bing', 'duckduckbot']
        );

        $result = $attribute->toArray();

        self::assertIsArray($result);
        self::assertTrue($result['allow']);
        self::assertSame(['google', 'bing', 'duckduckbot'], $result['allowList']);
    }
}
