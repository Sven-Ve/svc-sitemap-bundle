<?php

/*
 * This file is part of the SvcSitemapBundle package.
 *
 * (c) Sven Vetter <https://github.com/Sven-Ve/svc-sitemap-bundle>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Svc\SitemapBundle\Exception;

/**
 * exception.
 */
final class TranslationNotEnabled extends _SitemapException
{
  /**
   * @var string
   */
  protected $message = 'Translation for sitemap.xml not enabled, but localized links found';
}
