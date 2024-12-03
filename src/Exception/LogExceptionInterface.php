<?php

namespace Svc\SitemapBundle\Exception;

/**
 * An exception that is thrown by SvcSitemapBundle.
 *
 * @author Sven Vetter <dev@sv-systems.com>
 */
interface LogExceptionInterface extends \Throwable
{
  /**
   * Returns a safe string that describes why verification failed.
   */
  public function getReason(): string;
}
