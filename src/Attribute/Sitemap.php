<?php

declare(strict_types=1);

/*
 * This file is part of the SvcSitemap bundle.
 *
 * (c) 2026 Sven Vetter <dev@sv-systems.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Svc\SitemapBundle\Attribute;

use Svc\SitemapBundle\Enum\ChangeFreq;

/**
 * Attribute to configure sitemap settings for routes.
 *
 * Usage:
 *
 * ```php
 * #[Route('/path', name: 'route_name')]
 * #[Sitemap(priority: 0.8, changeFreq: ChangeFreq::DAILY)]
 * public function myAction(): Response
 * {
 *     // ...
 * }
 * ```
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
final class Sitemap
{
    /**
     * @param float|null      $priority   Priority (0.0 to 1.0), null to use default
     * @param ChangeFreq|null $changeFreq Change frequency, null to use default
     * @param string|null     $lastMod    Last modification date (e.g., '2024-01-01'), null to use current date
     * @param bool            $enabled    Whether to include this route in sitemap (default: true)
     */
    public function __construct(
        public readonly ?float $priority = null,
        public readonly ?ChangeFreq $changeFreq = null,
        public readonly ?string $lastMod = null,
        public readonly bool $enabled = true,
    ) {
        if ($this->priority !== null && ($this->priority < 0.0 || $this->priority > 1.0)) {
            throw new \InvalidArgumentException(\sprintf('Priority must be between 0.0 and 1.0, got %s', $this->priority));
        }
    }

    /**
     * Converts the attribute to an array format compatible with route options.
     *
     * @return array<string, mixed>|bool
     */
    public function toArray(): array|bool
    {
        if (!$this->enabled) {
            return false;
        }

        $config = [];

        if ($this->priority !== null) {
            $config['priority'] = $this->priority;
        }

        if ($this->changeFreq !== null) {
            $config['changefreq'] = $this->changeFreq;
        }

        if ($this->lastMod !== null) {
            $config['lastmod'] = $this->lastMod;
        }

        return empty($config) ? true : $config;
    }
}
