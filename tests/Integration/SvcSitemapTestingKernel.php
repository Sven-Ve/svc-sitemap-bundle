<?php

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
    $routes->import(__DIR__ . '/routes/routes.yaml')->prefix('/test/');
  }
}
