<?php

namespace Svc\SitemapBundle\Robots;

use Svc\SitemapBundle\Entity\RobotsOptions;
use Svc\SitemapBundle\Exception\RobotsTranslationNotEnabled;
use Svc\SitemapBundle\Service\RouteHandler;
use Symfony\Component\Routing\RouteCollection;

const CRLF = "\n";

final class RobotsHelper
{
  private RouteCollection $routeCollection;

  /**
   * @param array<string> $alternateLocales
   */
  public function __construct(
    private RouteHandler $routeHandler,
    private bool $translationEnabled,
    string $defaultLocale,
    private array $alternateLocales,
  ) {
    if ($translationEnabled) {
      $this->alternateLocales = array_merge($alternateLocales, [$defaultLocale]);
    }
  }

  /**
   * @return RobotsOptions[]
   */
  public function findStaticRoutes(): array
  {
    $allRoutes = [];
    $this->routeCollection = $this->routeHandler->getRouteCollection();

    foreach ($this->routeCollection->all() as $name => $route) {
      $robotsOptions = RobotsRouteParser::parse($name, $route);

      if (!$robotsOptions) {
        continue;
      }

      $allRoutes[] = $robotsOptions;
    }

    return $allRoutes;
  }

  /**
   * @param RobotsOptions[] $allRoutes
   *
   * @return array<mixed>
   */
  public function createRobotsArray(array $allRoutes): array
  {
    $definitions = [];

    foreach ($allRoutes as $route) {
      if ($route->getAllow()) {
        foreach ($route->getAllowList() as $userAgent) {
          $path = $route->getPath();
          if (!str_contains($path, '{_locale}')) {
            $definitions[$userAgent]['allow'][$path] = 1;
          } else {
            if (!$this->translationEnabled) {
              throw new RobotsTranslationNotEnabled();
            }
            foreach ($this->alternateLocales as $locale) {
              $localePath = str_replace('{_locale}', $locale, $path);
              $definitions[$userAgent]['allow'][$localePath] = 1;
            }
          }
        }
      }

      if ($route->getDisAllow()) {
        foreach ($route->getDisAllowList() as $userAgent) {
          $path = $route->getPath();
          if (!str_contains($path, '{_locale}')) {
            $definitions[$userAgent]['disallow'][$path] = 1;
          } else {
            if (!$this->translationEnabled) {
              throw new RobotsTranslationNotEnabled();
            }
            foreach ($this->alternateLocales as $locale) {
              $localePath = str_replace('{_locale}', $locale, $path);
              $definitions[$userAgent]['disallow'][$localePath] = 1;
            }
          }
        }
      }
    }

    return $definitions;
  }

  /**
   * Create the content of robots.txt as a string.
   *
   * @param array<mixed> $robArray
   *
   * @return array<mixed>
   *                      [0] = content
   *                      [1] = count of user agents
   */
  public function createRobotsText(array $robArray): array
  {
    $robTxt = '';
    foreach ($robArray as $userAgent => $definitions) {
      $robTxt .= 'User-agent: ' . $userAgent . CRLF;
      $robTxt .= $this->getAllPaths($definitions, 'allow');
      $robTxt .= $this->getAllPaths($definitions, 'disallow');
      $robTxt .= CRLF;
    }

    return [$robTxt, count($robArray)];
  }

  /**
   * @param array<mixed> $definitions
   * @param string       $filter      allow|disallow
   */
  private function getAllPaths(array $definitions, string $filter): string
  {
    $robTxt = '';
    if (array_key_exists($filter, $definitions)) {
      foreach (array_keys($definitions[$filter]) as $path) {
        $robTxt .= ucfirst($filter) . ': ' . $path . CRLF;
      }
    }

    return $robTxt;
  }
}
