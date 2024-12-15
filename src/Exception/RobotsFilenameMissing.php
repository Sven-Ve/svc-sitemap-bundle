<?php

namespace Svc\SitemapBundle\Exception;

/**
 * @author Sven Vetter <dev@sv-systems.com>
 */
final class RobotsFilenameMissing extends _SitemapException
{
  /**
   * @var string
   */
  protected $message = 'Name and/or directory for robots.txt is missing.';
}
