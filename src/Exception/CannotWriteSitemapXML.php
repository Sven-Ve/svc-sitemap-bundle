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

namespace Svc\SitemapBundle\Exception;

/**
 * exception.
 */
final class CannotWriteSitemapXML extends _SitemapException
{
    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        $finalMessage = $message !== '' ? $message : 'Cannot write sitemap.xml';

        parent::__construct($finalMessage, $code, $previous);
    }
}
