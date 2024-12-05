<?php

namespace Svc\SitemapBundle;

use Svc\SitemapBundle\Enum\ChangeFreq;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
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
        ->arrayNode('default_values')->info('define the default values for the sitemap file')
          ->addDefaultsIfNotSet()
          ->children()
            ->enumNode('change_freq')
              ->values(ChangeFreq::cases())
              ->defaultValue(ChangeFreq::WEEKLY)
              ->info('Standard change frequency, if not specified in the route')
              ->cannotBeEmpty()
            ->end()
            ->integerNode('priority')
              ->min(0)->max(1)
              ->defaultValue(0.5)
              ->info('Standard priority (between 0 and 1)')
            ->end()
          ->end()
        ->end()
        ->scalarNode('sitemap_directory')
          ->info('The directory in which the sitemap will be created.')
          ->defaultValue('%kernel.project_dir%/public')
          ->cannotBeEmpty()
        ->end()
        ->scalarNode('sitemap_filename')
          ->info('Filename of the sitemap file.')
          ->defaultValue('sitemap.xml')
          ->cannotBeEmpty()
        ->end()
        ->append($this->addTranslationNode())
      ->end()
    ->end();
  }

  private function addTranslationNode(): NodeDefinition
  {
    $treeBuilder = new TreeBuilder('translation');

    $node = $treeBuilder->getRootNode()
      ->info('Shoud alternate/translated urls used?')
      ->addDefaultsIfNotSet()
      ->canBeEnabled()
      ->children()
        ->scalarNode('default_language')
          ->info('set the default language for translated urls')
          ->defaultValue('en')
          ->cannotBeEmpty()
        ->end()
      ->end()
    ;

    return $node;
  }

  /**
   * @param array<mixed> $config
   */
  public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
  {
    $container->import('../config/services.yaml');

    $container->services()
      ->get('Svc\SitemapBundle\Service\SitemapHelper')
      ->arg(1, $config['default_values']['change_freq'])
      ->arg(2, $config['default_values']['priority'])
      ->arg(3, $config['translation']['enabled'])
    ;

    $container->services()
      ->get('Svc\SitemapBundle\Service\SitemapCreator')
      ->arg(2, $config['sitemap_directory'])
      ->arg(3, $config['sitemap_filename'])
    ;
  }
}
