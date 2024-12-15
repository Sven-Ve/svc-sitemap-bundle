<?php

namespace Svc\SitemapBundle\Exception;

/**
 * @author Sven Vetter <dev@sv-systems.com>
 */
final class RobotsTranslationNotEnabled extends _SitemapException
{
  /**
   * @var string
   */
  protected $message = 'Translation for robots.txt not enabled, but localized links found';
}
