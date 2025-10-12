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

namespace Svc\SitemapBundle\Tests\Unit\Robots;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Svc\SitemapBundle\Entity\RobotsOptions;
use Svc\SitemapBundle\Robots\RobotsRouteParser;
use Symfony\Component\Routing\Route;

/**
 * test the route parser (for robots.xml).
 */
final class RobotsRouteParserTest extends TestCase
{
    #[DataProvider('notRegisteredOptions')]
    public function testNotRegisteredOptions(bool|string|null $option): void
    {
        $result = RobotsRouteParser::parse('route_name', $this->getRoute($option));

        self::assertNull($result, 'Not registered to robots.txt');
    }

    /**
     * @param bool|string|array<mixed> $option
     * @param array<mixed>|null        $allowList
     * @param array<mixed>|null        $disallowList
     */
    #[DataProvider('registeredOptions')]
    public function testRouteParserRegistered(
        bool|string|array $option,
        ?bool $allow,
        ?bool $disallow,
        ?array $allowList,
        ?array $disallowList,
    ): void {
        $route = $this->getRoute($option);
        $result = RobotsRouteParser::parse('route_name', $route);

        self::assertInstanceOf(RobotsOptions::class, $result);

        self::assertEquals($allow, $result->getAllow(), '"allow" option is as expected');
        self::assertEquals($disallow, $result->getDisallow(), '"disallow" option is as expected');
        self::assertEquals($allowList, $result->getAllowList(), '"allowList" option is as expected');
        self::assertEquals($disallowList, $result->getDisallowList(), '"disallowList" option is as expected');
        self::assertEquals($route->getPath(), $result->getPath(), '"disallowList" option is as expected');
    }

    public static function registeredOptions(): \Generator
    {
        yield [true, false, false, ['*'], ['*']];
        yield ['yes', false, false, ['*'], ['*']];

        yield [['allow' => false, 'disallow' => false], false, false, ['*'], ['*']];
        yield [['allow' => true], true, false, ['*'], ['*']];
        yield [['disallow' => true], false, true, ['*'], ['*']];
        yield [['allow' => true, 'disallow' => true], true, true, ['*'], ['*']];

        yield [['allow' => true, 'allowList' => 'test1'], true, false, ['test1'], ['*']];
        yield [['allow' => true, 'allowList' => ['test1']], true, false, ['test1'], ['*']];
        yield [['allow' => true, 'allowList' => ['test1', 'test2']], true, false, ['test1', 'test2'], ['*']];

        yield [['disallow' => true, 'disallowList' => 'test1'], false, true, ['*'], ['test1']];
        yield [['disallow' => true, 'disallowList' => ['test1']], false, true, ['*'], ['test1']];
        yield [['disallow' => true, 'disallowList' => ['test1', 'test2']], false, true, ['*'], ['test1', 'test2']];
    }

    public static function notRegisteredOptions(): \Generator
    {
        yield [null];
        yield [false];
        yield ['no'];
    }

    /**
     * @param bool|string|array<mixed>|null $option
     */
    private function getRoute(bool|string|array|null $option): Route
    {
        return new Route('/', [], [], ['robots_txt' => $option]);
    }

    public function testAttributeWithAllowGoogleBing(): void
    {
        $route = new Route('/', ['_controller' => TestController::class . '::allowGoogleBing']);
        $result = RobotsRouteParser::parse('route_name', $route);

        self::assertNotNull($result);
        self::assertInstanceOf(RobotsOptions::class, $result);
        self::assertTrue($result->getAllow());
        self::assertFalse($result->getDisallow());
        self::assertSame(['google', 'bing'], $result->getAllowList());
    }

    public function testAttributeWithDisallowAll(): void
    {
        $route = new Route('/', ['_controller' => TestController::class . '::disallowAll']);
        $result = RobotsRouteParser::parse('route_name', $route);

        self::assertNotNull($result);
        self::assertInstanceOf(RobotsOptions::class, $result);
        self::assertFalse($result->getAllow());
        self::assertTrue($result->getDisallow());
        self::assertSame(['*'], $result->getDisallowList());
    }

    public function testAttributeWithAllowOnlyGoogle(): void
    {
        $route = new Route('/', ['_controller' => TestController::class . '::allowOnlyGoogle']);
        $result = RobotsRouteParser::parse('route_name', $route);

        self::assertNotNull($result);
        self::assertInstanceOf(RobotsOptions::class, $result);
        self::assertTrue($result->getAllow());
        self::assertSame(['google'], $result->getAllowList());
    }

    public function testAttributeDisabled(): void
    {
        $route = new Route('/', ['_controller' => TestController::class . '::withDisabledAttribute']);
        $result = RobotsRouteParser::parse('route_name', $route);

        self::assertNull($result, 'Disabled attribute should return null');
    }

    public function testAttributeWithDefaults(): void
    {
        $route = new Route('/', ['_controller' => TestController::class . '::withDefaultAttribute']);
        $result = RobotsRouteParser::parse('route_name', $route);

        self::assertNotNull($result);
        self::assertInstanceOf(RobotsOptions::class, $result);
        self::assertTrue($result->getAllow());
        self::assertSame(['*'], $result->getAllowList());
    }

    public function testWithoutAttribute(): void
    {
        $route = new Route('/', ['_controller' => TestController::class . '::withoutAttribute']);
        $result = RobotsRouteParser::parse('route_name', $route);

        self::assertNull($result, 'Route without attribute should return null');
    }

    public function testRouteOptionTakesPrecedenceOverAttribute(): void
    {
        $route = new Route(
            '/',
            ['_controller' => TestController::class . '::allowGoogleBing'],
            [],
            ['robots_txt' => ['allow' => true, 'allowList' => ['yahoo']]]
        );
        $result = RobotsRouteParser::parse('route_name', $route);

        self::assertNotNull($result);
        self::assertSame(['yahoo'], $result->getAllowList(), 'Route option should take precedence over attribute');
    }

    public function testInvalidControllerFormat(): void
    {
        $route = new Route('/', ['_controller' => 'invalid_format']);
        $result = RobotsRouteParser::parse('route_name', $route);

        self::assertNull($result);
    }

    public function testNonExistentClass(): void
    {
        $route = new Route('/', ['_controller' => 'NonExistent\\Class::method']);
        $result = RobotsRouteParser::parse('route_name', $route);

        self::assertNull($result);
    }
}
