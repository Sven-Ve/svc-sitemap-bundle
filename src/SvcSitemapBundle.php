<?php

declare(strict_types=1);

/*
 * This file is part of the SvcSitemap bundle.
 *
 * (c) 2025 Sven Vetter <dev@sv-systems.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Svc\SitemapBundle;

use Svc\SitemapBundle\Enum\ChangeFreq;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

/**
 * bundle definition.
 */
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
          ->append($this->addSitemapNode())
          ->append($this->addRobotsNode())
          ->end()
        ->end();
    }

    private function addSitemapNode(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder('sitemap');

        $node = $treeBuilder->getRootNode()
          ->info('Sitemap definition')
          ->addDefaultsIfNotSet()

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
                ->floatNode('priority')
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
        ->end();

        return $node;
    }

    private function addRobotsNode(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder('robots');

        $node = $treeBuilder->getRootNode()
          ->info('robots.txt definition')
          ->addDefaultsIfNotSet()

          ->children()
            ->scalarNode('robots_directory')
              ->info('The directory in which the robots.txt will be created.')
              ->defaultValue('%kernel.project_dir%/public')
              ->cannotBeEmpty()
            ->end()
            ->scalarNode('robots_filename')
              ->info('Filename of the robots.txt file.')
              ->defaultValue('robots.txt')
              ->cannotBeEmpty()
            ->end()
            ->scalarNode('sitemap_url')
              ->info('Optional: Full URL to sitemap.xml to include in robots.txt (e.g., https://example.com/sitemap.xml)')
              ->defaultValue(null)
            ->end()
          ->append($this->addTranslationNode())
        ->end();

        return $node;
    }

    private function addTranslationNode(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder('translation');

        $node = $treeBuilder->getRootNode()
          ->info('Shoud alternate/translated urls used?')
          ->addDefaultsIfNotSet()
          ->canBeEnabled()
          ->children()
            ->scalarNode('default_locale')
              ->info('set the default language for translated urls')
              ->defaultValue('en')
              ->cannotBeEmpty()
            ->end()

            ->arrayNode('locales')
              ->beforeNormalization()
              ->ifString()
                  ->then(
                      function ($v) {
                          return preg_split('/\s*,\s*/', $v);
                      }
                  )
              ->end()
              ->prototype('scalar')->end()
              ->info('List of supported locales')
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
          ->get('Svc\SitemapBundle\Sitemap\SitemapHelper')
          ->arg(2, $config['sitemap']['default_values']['change_freq'])
          ->arg(3, $config['sitemap']['default_values']['priority'])
          ->arg(4, $config['sitemap']['translation']['enabled'])
          ->arg(5, $config['sitemap']['translation']['default_locale'])
          ->arg(6, $config['sitemap']['translation']['locales'])
        ;

        $container->services()
          ->get('Svc\SitemapBundle\Sitemap\SitemapCreator')
          ->arg(2, $config['sitemap']['sitemap_directory'])
          ->arg(3, $config['sitemap']['sitemap_filename'])
          ->arg(4, $config['sitemap']['translation']['enabled'])
        ;

        $container->services()
          ->get('Svc\SitemapBundle\Robots\RobotsHelper')
          ->arg(1, $config['robots']['translation']['enabled'])
          ->arg(2, $config['robots']['translation']['default_locale'])
          ->arg(3, $config['robots']['translation']['locales'])
        ;

        $container->services()
          ->get('Svc\SitemapBundle\Robots\RobotsCreator')
          ->arg(2, $config['robots']['robots_directory'])
          ->arg(3, $config['robots']['robots_filename'])
          ->arg(4, $config['robots']['sitemap_url'])
        ;
    }
}
