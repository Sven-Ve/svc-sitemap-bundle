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

namespace Svc\SitemapBundle\Tests\Unit\Sitemap;

use Svc\SitemapBundle\Attribute\Sitemap;
use Svc\SitemapBundle\Enum\ChangeFreq;
use Symfony\Component\HttpFoundation\Response;

/**
 * Test controller for testing attribute parsing.
 */
class TestController
{
    #[Sitemap(priority: 0.8, changeFreq: ChangeFreq::DAILY)]
    public function withAttribute(): Response
    {
        return new Response();
    }

    #[Sitemap(priority: 0.5, changeFreq: ChangeFreq::WEEKLY, lastMod: '2024-01-01')]
    public function withFullAttribute(): Response
    {
        return new Response();
    }

    #[Sitemap(enabled: false)]
    public function withDisabledAttribute(): Response
    {
        return new Response();
    }

    #[Sitemap]
    public function withEmptyAttribute(): Response
    {
        return new Response();
    }

    public function withoutAttribute(): Response
    {
        return new Response();
    }
}
