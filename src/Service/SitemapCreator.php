<?php

namespace Svc\SitemapBundle\Service;

use Svc\SitemapBundle\Event\AddDynamicRoutesEvent;
use Svc\SitemapBundle\Exception\CannotWriteSitemapXML;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Filesystem;

final class SitemapCreator
{
  public function __construct(
    private EventDispatcherInterface $eventDispatcher,
    private SitemapHelper $sitemapHelper,
    private string $siteMapDir,
    private string $siteMapFile,
  ) {
  }

  /**
   * @return array<mixed>
   */
  public function create(): array
  {
    $staticRoutes = $this->sitemapHelper->findStaticRoutes();

    $dynamicRoutes = [];
    $event = new AddDynamicRoutesEvent($dynamicRoutes);
    $this->eventDispatcher->dispatch($event);

    $allRoutes = array_merge($staticRoutes, $event->getUrlContainer());
    $routeCount = count($allRoutes);

    return [CreateXML::create($this->sitemapHelper->normalizeRoutes($allRoutes)), $routeCount];
  }

  /**
   * create the static file public/sitemap.xml.
   *
   * @return array<mixed>
   */
  public function writeSitemapXML(?string $sitemapDir = null, ?string $sitemapFile = null): array
  {
    $sitemapDir ??= $this->siteMapDir;
    $sitemapFile ??= $this->siteMapFile;
    if (!str_ends_with($sitemapDir, DIRECTORY_SEPARATOR)) {
      $sitemapDir .= DIRECTORY_SEPARATOR;
    }
    $file = $sitemapDir . $sitemapFile;

    $filesystem = new Filesystem();

    list($xml, $routeCount) = $this->create();
    if ($xml and is_string($xml)) {
      try {
        $filesystem->dumpFile($file, $xml);
      } catch (\Exception $e) {
        throw new CannotWriteSitemapXML($e->getMessage());
      }

      return [$routeCount, $file];
    } else {
      return [0, null];
    }
  }
}
