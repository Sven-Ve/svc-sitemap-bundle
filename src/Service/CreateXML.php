<?php

namespace Svc\SitemapBundle\Service;

use Svc\SitemapBundle\Entity\RouteOptions;

final class CreateXML
{
  /**
   * @param array<RouteOptions> $routes
   */
  public static function create(array $routes): string|bool
  {
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
    /* @phpstan-ignore method.notFound */
    $urlset->setAttribute('xmlns:xhtml', $xmlns['xmlns:xhtml']);

    foreach ($routes as $route) {
      $url_node = $urlset->appendChild(
        $document->createElementNS($xmlns['sitemap'], 'url')
      );

      $url_node
        ->appendChild($document->createElementNS($xmlns['sitemap'], 'loc'))
        ->textContent = $route->getUrl();
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
      if ($route->getAlternates()) {
        foreach ($route->getAlternates() as $lang => $url) {
          $alternate = $document->createElement('xhtml:link');
          $alternate->setAttribute('rel', 'alternate');
          $alternate->setAttribute('hreflang', $lang);
          $alternate->setAttribute('href', $url);
          $url_node->appendChild($alternate);
        }

        // $url_node
        //   ->appendChild($document->createElementNS($xmlns['xmlns:xhtml'], 'xhtml:link'))
        //     ->appendChild($document->createAttribute("rel", "alternate"))
        //   ;
      }
    }

    return $document->saveXML();
  }
}
