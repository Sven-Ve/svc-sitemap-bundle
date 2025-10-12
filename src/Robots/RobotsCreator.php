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

namespace Svc\SitemapBundle\Robots;

use Svc\SitemapBundle\Event\AddRobotsTxtEvent;
use Svc\SitemapBundle\Exception\CannotWriteSitemapXML;
use Svc\SitemapBundle\Exception\RobotsFilenameMissing;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * create the robots.txt as file or string.
 */
final class RobotsCreator
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private RobotsHelper $robotsHelper,
        private string $robotsDir,
        private string $robotsFile,
        private ?string $sitemapUrl = null,
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
              ),
              $this->sitemapUrl
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
        try {
            $filesystem->dumpFile($file, $text);
        } catch (\Exception $e) {
            throw new CannotWriteSitemapXML(\sprintf('Cannot write robots.txt to %s: %s', $file, $e->getMessage()), 0, $e);
        }

        return [$userAgentCount, $file];

    }
}
