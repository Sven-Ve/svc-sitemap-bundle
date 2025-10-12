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
        $routeOptions->setPriority((float) $options['priority']);

        return $routeOptions;
    }
}
