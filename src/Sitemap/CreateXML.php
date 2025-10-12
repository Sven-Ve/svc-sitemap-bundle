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
use Svc\SitemapBundle\Exception\SitemapTooLargeException;

/**
 * create the XML structure for sitemap.xml.
 */
final class CreateXML
{
    /**
     * Validates that a string is valid UTF-8.
     *
     * @throws \InvalidArgumentException if string is not valid UTF-8
     */
    private static function validateUtf8(string $text, string $fieldName): void
    {
        if (!\mb_check_encoding($text, 'UTF-8')) {
            throw new \InvalidArgumentException(\sprintf('Invalid UTF-8 encoding in %s: %s', $fieldName, \mb_substr($text, 0, 50) . (\mb_strlen($text) > 50 ? '...' : '')));
        }
    }

    /**
     * Validates and sanitizes a URL for use in sitemap XML.
     *
     * @throws \InvalidArgumentException if URL is invalid
     */
    private static function validateUrl(string $url): string
    {
        // Validate UTF-8 encoding
        self::validateUtf8($url, 'URL');

        // Validate URL format
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException(\sprintf('Invalid URL format: %s', $url));
        }

        // Parse and validate URL scheme
        $parsed = \parse_url($url);
        if ($parsed === false || !isset($parsed['scheme'])) {
            throw new \InvalidArgumentException(\sprintf('Cannot parse URL: %s', $url));
        }

        // Only allow http and https schemes (security: prevent javascript:, data:, etc.)
        if (!\in_array($parsed['scheme'], ['http', 'https'], true)) {
            throw new \InvalidArgumentException(\sprintf('Invalid URL scheme "%s" in URL: %s. Only http and https are allowed.', $parsed['scheme'], $url));
        }

        // Ensure hostname is present
        if (!isset($parsed['host']) || $parsed['host'] === '') {
            throw new \InvalidArgumentException(\sprintf('URL must contain a hostname: %s', $url));
        }

        return $url;
    }

    /**
     * @param array<RouteOptions> $routes
     *
     * @throws \RuntimeException         if XML generation fails
     * @throws \InvalidArgumentException if any URL is invalid
     * @throws SitemapTooLargeException  if sitemap exceeds size limits
     */
    public static function create(array $routes, bool $translationEnabled): string
    {
        // Validate URL count (max 50,000 URLs per sitemap)
        $urlCount = \count($routes);
        if ($urlCount > SitemapTooLargeException::MAX_URLS) {
            throw SitemapTooLargeException::tooManyUrls($urlCount);
        }
        $xmlns = [
            'sitemap' => 'http://www.sitemaps.org/schemas/sitemap/0.9',
            'xmlns' => 'http://www.w3.org/2000/xmlns/',
            'xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
            'xsi:schemaLocation' => 'http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd',
            'xmlns:xhtml' => 'http://www.w3.org/1999/xhtml',
        ];

        $document = new \DOMDocument('1.0', 'utf-8');
        $document->formatOutput = true;

        $urlset = $document->appendChild(
            $document->createElementNS($xmlns['sitemap'], 'urlset')
        );

        // explict namespace definition
        /* @phpstan-ignore method.notFound */
        $urlset->setAttributeNS(
            $xmlns['xmlns'],
            'xmlns:xsi',
            $xmlns['xsi']
        );
        /* @phpstan-ignore method.notFound */
        $urlset->setAttribute('xsi:schemaLocation', $xmlns['xsi:schemaLocation']);

        if ($translationEnabled) {
            /* @phpstan-ignore method.notFound */
            $urlset->setAttribute('xmlns:xhtml', $xmlns['xmlns:xhtml']);
        }

        foreach ($routes as $route) {
            $url_node = $urlset->appendChild(
                $document->createElementNS($xmlns['sitemap'], 'url')
            );

            // Validate main URL
            $mainUrl = $route->getUrl();
            if ($mainUrl === null) {
                throw new \InvalidArgumentException(\sprintf('Route "%s" has no URL set', $route->getRouteName()));
            }

            $url_node
              ->appendChild($document->createElementNS($xmlns['sitemap'], 'loc'))
              ->textContent = self::validateUrl($mainUrl);
            $url_node
              ->appendChild($document->createElementNS($xmlns['sitemap'], 'lastmod'))
              ->textContent = $route->getLastModXMLFormat();
            $url_node
              ->appendChild($document->createElementNS($xmlns['sitemap'], 'changefreq'))
              ->textContent = $route->getChangeFreqText();
            if ($route->getPriority() !== null and $route->getPriority() != 0.5) {
                $url_node
                  ->appendChild($document->createElementNS($xmlns['sitemap'], 'priority'))
                  ->textContent = number_format($route->getPriority(), 1);
            }
            if ($translationEnabled and $route->getAlternates()) {
                foreach ($route->getAlternates() as $lang => $url) {
                    // Validate alternate URL
                    $validatedAltUrl = self::validateUrl($url);

                    $alternate = $document->createElement('xhtml:link');
                    $alternate->setAttribute('rel', 'alternate');
                    $alternate->setAttribute('hreflang', $lang);
                    $alternate->setAttribute('href', $validatedAltUrl);
                    $url_node->appendChild($alternate);
                }
            }
        }

        $xml = $document->saveXML();
        if ($xml === false) {
            throw new \RuntimeException('Failed to generate XML document');
        }

        // Validate size (max 50MB uncompressed)
        $xmlSize = \strlen($xml);
        if ($xmlSize > SitemapTooLargeException::MAX_SIZE_BYTES) {
            throw SitemapTooLargeException::tooLargeSize($xmlSize);
        }

        return $xml;
    }
}
