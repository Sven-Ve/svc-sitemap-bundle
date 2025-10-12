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

namespace Svc\SitemapBundle\Attribute;

/**
 * Attribute to configure robots.txt settings for routes.
 *
 * Usage:
 *
 * ```php
 * #[Route('/admin', name: 'admin')]
 * #[Robots(allow: false, userAgents: ['*'])]
 * public function adminAction(): Response
 * {
 *     // ...
 * }
 * ```
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
final class Robots
{
    /**
     * @param bool                $allow      Whether to allow access (true = Allow, false = Disallow)
     * @param array<string>|null  $userAgents List of user agents this rule applies to (default: ['*'])
     * @param bool                $enabled    Whether to include this route in robots.txt (default: true)
     */
    public function __construct(
        public readonly bool $allow = true,
        public readonly ?array $userAgents = null,
        public readonly bool $enabled = true,
    ) {
        // Validate user agents array - only check types that aren't string
        if ($this->userAgents !== null) {
            foreach ($this->userAgents as $userAgent) {
                // @phpstan-ignore-next-line - We validate at runtime for safety even though PHPDoc says array<string>
                if (!\is_string($userAgent)) {
                    throw new \InvalidArgumentException(\sprintf('User agent must be a string, got %s', \gettype($userAgent)));
                }
            }
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

        if ($this->allow) {
            $config['allow'] = true;
            $config['allowList'] = $this->userAgents ?? ['*'];
        } else {
            $config['disallow'] = true;
            $config['disallowList'] = $this->userAgents ?? ['*'];
        }

        return $config;
    }
}
