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

namespace Svc\SitemapBundle\Exception;

/**
 * exception.
 */
final class RobotsFilenameMissing extends _SitemapException
{
    /**
     * @var string
     */
    protected $message = 'Name and/or directory for robots.txt is missing.';
}
