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

namespace Svc\SitemapBundle\Event;

use Svc\SitemapBundle\Entity\RouteOptions;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * event handler to add routes to sitemap.xml.
 */
final class AddDynamicRoutesEvent extends Event
{
    /**
     * @param array<RouteOptions> $urlContainer
     */
    public function __construct(
        private array $urlContainer,
    ) {
    }

    /**
     * @return array<RouteOptions>
     */
    public function getUrlContainer(): array
    {
        return $this->urlContainer;
    }

    /**
     * add a new URL to the container.
     */
    public function addUrlToContainer(RouteOptions $url): self
    {
        $this->urlContainer[] = $url;

        return $this;
    }
}
