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
 * Exception thrown when sitemap exceeds size limits.
 */
final class SitemapTooLargeException extends _SitemapException
{
    public const MAX_URLS = 50000;

    public const MAX_SIZE_BYTES = 52428800; // 50MB

    public static function tooManyUrls(int $count): self
    {
        return new self(\sprintf(
            'Sitemap contains %d URLs, but the maximum allowed is %d. Consider splitting into multiple sitemaps.',
            $count,
            self::MAX_URLS
        ));
    }

    public static function tooLargeSize(int $sizeBytes): self
    {
        $sizeMb = round($sizeBytes / 1024 / 1024, 2);

        return new self(\sprintf(
            'Sitemap size is %.2f MB, but the maximum allowed is 50 MB. Consider splitting into multiple sitemaps.',
            $sizeMb
        ));
    }
}
