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
  protected $message = 'Translation not enabled, but localized links found';
}
