<?php

namespace Svc\SitemapBundle\Service;

use Svc\SitemapBundle\Entity\RouteOptions;
use Svc\SitemapBundle\Enum\ChangeFreq;
use Svc\SitemapBundle\Exception\TranslationNotEnabled;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Symfony\Component\Routing\RouterInterface;

final class SitemapHelper
{
  public function __construct(
    private RouterInterface $router,
    private ChangeFreq $defaultChangeFreq,
    private float $defaultPriority,
    private bool $translationEnabled,
  ) {
  }

  /**
   * @return RouteOptions[]
   */
  public function findStaticRoutes(): array
  {
    $collection = $this->router->getRouteCollection();
    $allRoutes = [];

    foreach ($collection->all() as $name => $route) {
      $routeOptions = RouteParser::parse($name, $route);

      if (!$routeOptions) {
        continue;
      }

      $allRoutes[] = $routeOptions;
    }

    return $allRoutes;
  }

  /**
   * @param array<string, mixed> $routeParam
   */
  public function generateURL(string $routeName, array $routeParam): ?string
  {
    $url = null;
    try {
      $url = $this->router->generate($routeName, $routeParam, RouterInterface::ABSOLUTE_URL);
    } catch (MissingMandatoryParametersException $e) {
      if (in_array('_locale', $e->getMissingParameters())) {
        if ($this->translationEnabled) {
          $routeParam['_locale'] = 'de';
          $url = $this->router->generate($routeName, $routeParam, RouterInterface::ABSOLUTE_URL);
        } else {
          throw new TranslationNotEnabled(sprintf('Translation not enabled, but localized routes found (%s)', $routeName));
        }
      }
    }

    return $url;
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
      $url = $this->generateURL($route->getRouteName(), $route->getRouteParam());
      $route->setUrl($url);

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
