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

namespace Svc\SitemapBundle\Tests\Unit\Robots;

use Svc\SitemapBundle\Attribute\Robots;
use Symfony\Component\HttpFoundation\Response;

/**
 * Test controller for testing robots attribute parsing.
 */
class TestController
{
    #[Robots(allow: true, userAgents: ['google', 'bing'])]
    public function allowGoogleBing(): Response
    {
        return new Response();
    }

    #[Robots(allow: false, userAgents: ['*'])]
    public function disallowAll(): Response
    {
        return new Response();
    }

    #[Robots(allow: true, userAgents: ['google'])]
    public function allowOnlyGoogle(): Response
    {
        return new Response();
    }

    #[Robots(enabled: false)]
    public function withDisabledAttribute(): Response
    {
        return new Response();
    }

    #[Robots]
    public function withDefaultAttribute(): Response
    {
        return new Response();
    }

    public function withoutAttribute(): Response
    {
        return new Response();
    }
}
