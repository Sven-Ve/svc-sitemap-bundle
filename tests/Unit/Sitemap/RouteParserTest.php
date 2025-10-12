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

namespace Svc\SitemapBundle\Tests\Unit\Sitemap;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Svc\SitemapBundle\Entity\RouteOptions;
use Svc\SitemapBundle\Enum\ChangeFreq;
use Svc\SitemapBundle\Sitemap\RouteParser;
use Symfony\Component\Routing\Route;

/**
 * test the route parser (for sitemap.xml).
 */
class RouteParserTest extends TestCase
{
    public function testInvalidRouteOption(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        RouteParser::parse('route1', $this->getRoute('anything'));
    }

    public function testInvalidLastmodRouteOption(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        RouteParser::parse('route1', $this->getRoute(['lastmod' => 'unknown']));
    }

    #[DataProvider('notRegisteredOptions')]
    public function testNotRegisteredOptions(false|string|null $option): void
    {
        $options = RouteParser::parse('route_name', $this->getRoute($option));

        self::assertNull($options, 'Not registered to sitemap');
    }

    /**
     * @param bool|string|array<mixed>|null $option
     */
    #[DataProvider('registeredOptions')]
    public function testRegisteredOptions(
        bool|string|array|null $option,
        ?\DateTimeImmutable $lastmod,
        ?string $changefreq,
        ?float $priority,
    ): void {
        $options = RouteParser::parse('route_name', $this->getRoute($option));

        self::assertNotNull($options, 'Registered to sitemap');
        self::assertInstanceOf(RouteOptions::class, $options);

        self::assertEquals($lastmod, $options->getLastMod(), '"lastmod" option is as expected');
        self::assertSame($changefreq, $options->getChangeFreqText(), '"changefreq" option is as expected');
        self::assertSame($priority, $options->getPriority(), '"priority" option is as expected');
    }

    public static function notRegisteredOptions(): \Generator
    {
        yield [null];
        yield [false];
        yield ['no'];
    }

    public static function registeredOptions(): \Generator
    {
        yield [true, null, null, 0];
        yield ['yes', null, null, 0];
        yield [['priority' => 0.5], null, null, 0.5];
        yield [['changefreq' => ChangeFreq::WEEKLY], null, 'weekly', 0];
        yield [['lastmod' => '2012-01-01 00:00:00'], new \DateTimeImmutable('2012-01-01 00:00:00'), null, 0];

        return;
    }

    /**
     * @param bool|string|array<mixed>|null $option
     */
    private function getRoute(bool|string|array|null $option): Route
    {
        return new Route('/', [], [], ['sitemap' => $option]);
    }
}
