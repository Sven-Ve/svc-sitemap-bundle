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
}
