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

namespace Svc\SitemapBundle\Sitemap;

use Svc\SitemapBundle\Attribute\Sitemap;
use Svc\SitemapBundle\Entity\RouteOptions;
use Symfony\Component\Routing\Route;

/**
 * parse route options for sitemap.xml.
 */
final class RouteParser
{
    public static function parse(string $name, Route $route): ?RouteOptions
    {
        $option = $route->getOption('sitemap');

        // Trigger deprecation notice if route options are used
        if ($option !== null) {
            @trigger_error(
                \sprintf(
                    'Using route option "sitemap" for route "%s" is deprecated since SvcSitemapBundle 1.2 and will be removed in 2.0. Use the #[Sitemap] attribute instead.',
                    $name
                ),
                E_USER_DEPRECATED
            );
        }

        // Check for #[Sitemap] attribute on controller method
        if ($option === null) {
            $option = self::getAttributeFromController($route);
        }

        if ($option === null) {
            return null;
        }

        $routeOptions = new RouteOptions($name);

        if (\is_string($option)) {
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
            $routeOptions->setLastMod($lastmod);
        }
        $routeOptions->setChangeFreq($options['changefreq']);
        if ($options['priority'] !== null) {
            $routeOptions->setPriority((float) $options['priority']);
        }

        return $routeOptions;
    }

    /**
     * Extracts sitemap configuration from #[Sitemap] attribute on controller method.
     *
     * @return array<string, mixed>|bool|null
     */
    private static function getAttributeFromController(Route $route): array|bool|null
    {
        $controller = $route->getDefault('_controller');

        if (!\is_string($controller) || !str_contains($controller, '::')) {
            return null;
        }

        [$class, $method] = explode('::', $controller, 2);

        if (!class_exists($class)) {
            return null;
        }

        try {
            $reflectionMethod = new \ReflectionMethod($class, $method);
        } catch (\ReflectionException) {
            return null;
        }

        $attributes = $reflectionMethod->getAttributes(Sitemap::class);

        if (empty($attributes)) {
            return null;
        }

        $attribute = $attributes[0]->newInstance();

        return $attribute->toArray();
    }
}
