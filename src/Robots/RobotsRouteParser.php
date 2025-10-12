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

namespace Svc\SitemapBundle\Robots;

use Svc\SitemapBundle\Entity\RobotsOptions;
use Symfony\Component\Routing\Route;

/**
 * parse route options for robots.txt.
 */
final class RobotsRouteParser
{
    public static function parse(string $name, Route $route): ?RobotsOptions
    {
        $option = $route->getOption('robots_txt');

        if ($option === null) {
            return null;
        }

        $robotsOptions = new RobotsOptions($name);

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
            'allow' => null,
            'disallow' => null,
            'allowList' => null,
            'disallowList' => null,
        ];
        if (\is_array($option)) {
            $options = \array_merge($options, $option);
        }

        $allow = $options['allow'];
        if ($allow) {
            if (!is_bool($allow)) {
                throw new \InvalidArgumentException(\sprintf('The route %s robot.txt allow option must be of type "boolean", got "%s"', $name, \gettype($allow)));
            }

            $robotsOptions->setAllow(true);

            $allowList = $options['allowList'];
            if ($allowList) {
                if (is_array($allowList)) {
                    $robotsOptions->setAllowList($allowList);
                } elseif (is_string($allowList)) {
                    $robotsOptions->setAllowList([$allowList]);
                } else {
                    throw new \InvalidArgumentException(\sprintf('The route %s robot.txt allowList option must be of type "array" or "string", got "%s"', $name, \gettype($allow)));
                }
            }
        }

        $disallow = $options['disallow'];
        if ($disallow) {
            if (!is_bool($disallow)) {
                throw new \InvalidArgumentException(\sprintf('The route %s robot.txt disallow option must be of type "boolean", got "%s"', $name, \gettype($disallow)));
            }

            $robotsOptions->setDisallow(true);

            $disallowList = $options['disallowList'];
            if ($disallowList) {
                if (is_array($disallowList)) {
                    $robotsOptions->setDisallowList($disallowList);
                } elseif (is_string($disallowList)) {
                    $robotsOptions->setDisallowList([$disallowList]);
                } else {
                    throw new \InvalidArgumentException(\sprintf('The route %s robot.txt disallowList option must be of type "array" or "string", got "%s"', $name, \gettype($disallow)));
                }
            }
        }

        $robotsOptions->setPath($route->getPath());

        return $robotsOptions;
    }
}
