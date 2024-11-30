<?php

namespace Svc\SitemapBundle\Service;

use Svc\SitemapBundle\Entity\RouteOptions;
use Symfony\Component\Routing\RouterInterface;

class CreateXML
{


  /**
   * Undocumented function
   *
   * @param array<RouteOptions> $routes
   * @return void
   */
  public static function create(array $routes): string
  {


    $xmlns = [
      'sitemap' => 'http://www.sitemaps.org/schemas/sitemap/0.9',
      'xmlns' => 'http://www.w3.org/2000/xmlns/',
      'xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
    ];

    $document = new \DOMDocument('1.0', 'utf-8');
    $document->formatOutput = true;

    $urlset = $document->appendChild(
      $document->createElementNS($xmlns['sitemap'], 'urlset')
    );
    // explict namespace definition
    // $urlset->setAttributeNS(
    //     $xmlns['xmlns'], 'xmlns:xsi', $xmlns['xsi']
    // );

    foreach ($routes as $route) {
      $url_node = $urlset->appendChild(
        $document->createElementNS($xmlns['sitemap'], 'url')
      );

      $url_node
        ->appendChild($document->createElementNS($xmlns['sitemap'], 'loc'))
        ->textContent = $route->url;
      $url_node
        ->appendChild($document->createElementNS($xmlns['sitemap'], 'lastmod'))
        ->textContent = $route->lastMod->format('c');
      $url_node
        ->appendChild($document->createElementNS($xmlns['sitemap'], 'changefreq'))
        ->textContent = $route->changeFreq->value;
      if ($route->priority !== null and $route->priority != 0.5) {
      $url_node
        ->appendChild($document->createElementNS($xmlns['sitemap'], 'priority'))
        ->textContent = $route->priority;
      }
    }

    return $document->saveXML();
  }
}
