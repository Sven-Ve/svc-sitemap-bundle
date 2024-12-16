<?php

/*
 * This file is part of the SvcSitemapBundle package.
 *
 * (c) Sven Vetter <https://github.com/Sven-Ve/svc-sitemap-bundle>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Svc\SitemapBundle\Enum;

/**
 * enum for change frequency in sitemap.xml.
 */
enum ChangeFreq: string
{
  case ALWAYS = 'always';
  case HOURLY = 'hourly';
  case DAILY = 'daily';
  case WEEKLY = 'weekly';
  case MONTHLY = 'monthly';
  case YEARLY = 'yearly';
  case NEVER = 'never';
}
