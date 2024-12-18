<?php

/*
 * This file is part of the SvcSitemapBundle package.
 *
 * (c) Sven Vetter <https://github.com/Sven-Ve/svc-sitemap-bundle>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Svc\SitemapBundle\Sitemap;

use Svc\SitemapBundle\Entity\RouteOptions;
use Svc\SitemapBundle\Enum\ChangeFreq;
use Svc\SitemapBundle\Exception\TranslationNotEnabled;
use Svc\SitemapBundle\Service\RouteHandler;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

/**
 * helper functions to create the sitemap.xml.
 */
final class SitemapHelper
{
  private RouteCollection $routeCollection;

  /**
   * @param array<string> $alternateLocales
   */
  public function __construct(
    private RouterInterface $router,
    private RouteHandler $routeHandler,
    private ChangeFreq $defaultChangeFreq,
    private float $defaultPriority,
    private bool $translationEnabled,
    private string $defaultLocale,
    private array $alternateLocales,
  ) {
  }

  /**
   * @return RouteOptions[]
   */
  public function findStaticRoutes(): array
  {
    $this->routeCollection = $this->routeHandler->getRouteCollection();
    $allRoutes = [];

    foreach ($this->routeCollection->all() as $name => $route) {
      $routeOptions = RouteParser::parse($name, $route);

      if (!$routeOptions) {
        continue;
      }

      $allRoutes[] = $routeOptions;
    }

    return $allRoutes;
  }

  private function generateURLs(RouteOptions $route): void
  {
    $url = null;
    $routeName = $route->getRouteName();
    $routeParam = $route->getRouteParam();
    $routePath = $this->routeCollection->get($routeName)->getPath();

    if (!str_contains($routePath, '{_locale}')) {
      $url = $this->router->generate($routeName, $routeParam, RouterInterface::ABSOLUTE_URL);
    } else {
      if ($this->translationEnabled) {
        $routeParam['_locale'] = $this->defaultLocale;
        $url = $this->router->generate($routeName, $routeParam, RouterInterface::ABSOLUTE_URL);

        $route->addAlternate($this->defaultLocale, $url);

        foreach ($this->alternateLocales as $lang) {
          $routeParam['_locale'] = $lang;
          $altUrl = $this->router->generate($routeName, $routeParam, RouterInterface::ABSOLUTE_URL);

          $route->addAlternate($lang, $altUrl);
        }
      } else {
        throw new TranslationNotEnabled(sprintf('Translation not enabled, but localized routes found (%s)', $routeName));
      }
    }

    $route->setUrl($url);
  }

  /**
   * fill url, fill empty fields with default values.
   *
   * @param array<RouteOptions> $routes
   *
   * @return array<RouteOptions>
   */
  public function normalizeRoutes(array $routes): array
  {
    foreach ($routes as $route) {
      $this->generateURLs($route);

      if (!$route->getLastMod()) {
        $route->setLastMod(new \DateTimeImmutable('now'));
      }

      if (!$route->getChangeFreq()) {
        $route->setChangeFreq($this->defaultChangeFreq);
      }

      if ($route->getPriority() === null) {
        $route->setPriority($this->defaultPriority);
      }
    }

    return $routes;
  }
}
