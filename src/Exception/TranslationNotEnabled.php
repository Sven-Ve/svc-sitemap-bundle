<?php

namespace Svc\SitemapBundle\Exception;

/**
 * @author Sven Vetter <dev@sv-systems.com>
 */
final class TranslationNotEnabled extends _SitemapException
{
  /**
   * @var string
   */
  protected $message = 'Translation for sitemap.xml not enabled, but localized links found';
}
