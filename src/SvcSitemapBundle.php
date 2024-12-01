<?php

namespace Svc\SitemapBundle;

use Svc\SitemapBundle\Enum\ChangeFreq;
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
        ->enumNode('default_change_freq')
          ->values(ChangeFreq::cases())
          ->defaultValue(ChangeFreq::WEEKLY)
          ->info('Standard change frequency, if not specified in the route')
          ->cannotBeEmpty()
        ->end()
        ->integerNode('default_priority')
          ->min(0)->max(1)
          ->defaultValue(0.5)
          ->info('Standard priority (between 0 and 1)')
        ->end()
      ->end()
    ->end();
  }

  /**
   * @param array<mixed> $config
   */
  public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
  {
    $container->import('../config/services.yaml');

    $container->services()
      ->get('Svc\SitemapBundle\Service\SitemapHelper')
      ->arg(2, $config['default_change_freq'])
      ->arg(3, $config['default_priority'])
    ;
  }
}
