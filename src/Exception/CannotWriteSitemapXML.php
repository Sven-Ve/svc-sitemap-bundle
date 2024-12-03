<?php

namespace Svc\SitemapBundle\Exception;

/**
 * @author Sven Vetter <dev@sv-systems.com>
 */
final class CannotWriteSitemapXML extends \Exception implements LogExceptionInterface
{
  /**
   * @var string
   */
  protected $message = 'Cannot write sitemap.xml';

  public function getReason(): string
  {
    return $this->message;
  }
}
