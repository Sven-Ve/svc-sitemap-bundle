<?php

namespace Svc\SitemapBundle;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class SvcSitemapBundle extends AbstractBundle
{
  public function getPath(): string
  {
    return \dirname(__DIR__);
  }

  public function configure(DefinitionConfigurator $definition): void
  {
    $definition->rootNode()
      ->children()
        ->end()
    ->end();
  }

  /**
   * @param array<mixed> $config
   */
  public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
  {
    $container->import('../config/services.yaml');
  }
}
