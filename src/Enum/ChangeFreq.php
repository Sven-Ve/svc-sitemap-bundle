<?php

namespace Svc\SitemapBundle\Enum;

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
