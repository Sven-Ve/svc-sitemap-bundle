<?php

/*
 * This file is part of the SvcSitemapBundle package.
 *
 * (c) Sven Vetter <https://github.com/Sven-Ve/svc-sitemap-bundle>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Svc\SitemapBundle\Tests\Integration;

use Svc\SitemapBundle\SvcSitemapBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

/**
 * Test kernel.
 */
final class SvcSitemapTestingKernel extends Kernel
{
  use MicroKernelTrait;

  public function registerBundles(): iterable
  {
    yield new SvcSitemapBundle();
    yield new FrameworkBundle();
  }

  protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
  {
    $config = [
      'http_method_override' => false,
      'secret' => 'foo-secret',
      'test' => true,
    ];

    $container->loadFromExtension('framework', $config);
  }

  /** @phpstan-ignore method.unused */
  private function configureRoutes(RoutingConfigurator $routes): void
  {
    $routes->import(__DIR__ . '/config/routes/routes.yaml')->prefix('/test/');
  }
}
