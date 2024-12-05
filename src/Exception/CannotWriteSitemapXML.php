<?php

namespace Svc\SitemapBundle\Exception;

/**
 * @author Sven Vetter <dev@sv-systems.com>
 */
final class CannotWriteSitemapXML extends _SitemapException
{
  /**
   * @var string
   */
  protected $message = 'Cannot write sitemap.xml';
}
