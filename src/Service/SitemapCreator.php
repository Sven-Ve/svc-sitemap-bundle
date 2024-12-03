<?php

namespace Svc\SitemapBundle\Service;

use Svc\SitemapBundle\Event\AddDynamicRoutesEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Filesystem;

final class SitemapCreator
{
  public function __construct(
    private EventDispatcherInterface $eventDispatcher,
    private SitemapHelper $sitemapHelper,
  ) {
  }

  public function create(): string|bool
  {
    $staticRoutes = $this->sitemapHelper->findStaticRoutes();

    $dynamicRoutes = [];
    $event = new AddDynamicRoutesEvent($dynamicRoutes);
    $this->eventDispatcher->dispatch($event);

    return CreateXML::create($this->sitemapHelper->normalizeRoutes(array_merge($staticRoutes, $event->getUrlContainer())));
  }

  /**
   * create the static file public/sitemap.xml.
   *
   * @return int number of urls
   */
  public function writeSitemapXML(): int
  {
    $filesystem = new Filesystem();

    $xml = $this->create();
    if ($xml and is_string($xml)) {
      $filesystem->dumpFile('public/sitemap.xml', $xml);

      return 1;
    } else {
      return 0;
    }
  }
}
