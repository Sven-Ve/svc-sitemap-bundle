<?php

namespace Svc\SitemapBundle\Service;

use Svc\SitemapBundle\Entity\RouteOptions;
use Symfony\Component\Routing\Route;

class RouteParser
{
  public static function parse(string $name, Route $route): ?RouteOptions
  {
    $option = $route->getOption('sitemap');

    if ($option === null) {
      return null;
    }

    $routeOptions = new RouteOptions($name);

    if (\is_string($option)) {
      if (!\function_exists('json_decode')) {
        throw new \RuntimeException(\sprintf('The route %s sitemap options are defined as JSON string, but PHP extension is missing.', $name));
      }
      $decoded = \json_decode($option, true);
      if (!\json_last_error() && \is_array($decoded)) {
        $option = $decoded;
      }
    }

    if (!\is_array($option) && !\is_bool($option)) {
      $bool = \filter_var($option, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

      if (null === $bool) {
        throw new \InvalidArgumentException(\sprintf('The route %s sitemap option must be of type "boolean" or "array", got "%s"', $name, \gettype($option)));
      }

      $option = $bool;
    }

    if (!$option) {
      return null;
    }

    $options = [
      'section' => null,
      'lastmod' => null,
      'changefreq' => null,
      'priority' => null,
    ];
    if (\is_array($option)) {
      $options = \array_merge($options, $option);
    }

    if (\is_string($options['lastmod'])) {
      try {
        $lastmod = new \DateTimeImmutable($options['lastmod']);
      } catch (\Exception $e) {
        throw new \InvalidArgumentException(\sprintf('The route %s has an invalid value "%s" specified for the "lastmod" option', $name, $options['lastmod']), 0, $e);
      }

      $options['lastmod'] = $lastmod;
      $routeOptions->lastMod = $lastmod;
      $routeOptions->changeFreq = $options['changefreq'];
      $routeOptions->priority = $options['priority'];
    }

    //    dump($options);
    return $routeOptions;
  }
}
