<?php

namespace Svc\SitemapBundle\Robots;

use Svc\SitemapBundle\Event\AddRobotsTxtEvent;
use Svc\SitemapBundle\Exception\CannotWriteSitemapXML;
use Svc\SitemapBundle\Exception\RobotsFilenameMissing;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Filesystem;

final class RobotsCreator
{
  public function __construct(
    private EventDispatcherInterface $eventDispatcher,
    private RobotsHelper $robotsHelper,
    private string $robotsDir,
    private string $robotsFile,
  ) {
  }

  /**
   * @return array<mixed>
   */
  public function create(): array
  {
    $staticDef = $this->robotsHelper->findStaticRoutes();

    $dynamicDef = [];
    $event = new AddRobotsTxtEvent($dynamicDef);
    $this->eventDispatcher->dispatch($event);

    return
      $this->robotsHelper->createRobotsText(
        $this->robotsHelper->createRobotsArray(
          array_merge($staticDef, $event->getRobotsContainer())
        )
      );
  }

  /**
   * create the static file public/robots.txt.
   *
   * @return array<mixed>
   */
  public function writeRobotsTxt(
    ?string $robotsDir = null,
    ?string $robotsFile = null,
  ): array {
    $robotsDir ??= $this->robotsDir;
    $robotsFile ??= $this->robotsFile;
    if (!str_ends_with($robotsDir, DIRECTORY_SEPARATOR)) {
      $robotsDir .= DIRECTORY_SEPARATOR;
    }
    $file = $robotsDir . $robotsFile;

    if ($file == DIRECTORY_SEPARATOR) {
      throw new RobotsFilenameMissing();
    }

    $filesystem = new Filesystem();

    list($text, $userAgentCount) = $this->create();
    if ($text and is_string($text)) {
      try {
        $filesystem->dumpFile($file, $text);
      } catch (\Exception $e) {
        throw new CannotWriteSitemapXML($e->getMessage());
      }

      return [$userAgentCount, $file];
    } else {
      return [0, null];
    }
  }
}
