<?php

/**
 * This file is part of the HateoasBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Bazinga\Bundle\HateoasBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author William Durand <william.durand1@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root('bazinga_hateoas');

        $rootNode
            ->children()
            ->arrayNode('metadata')
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('cache')->defaultValue('file')->end()
                    ->arrayNode('file_cache')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('dir')->defaultValue('%kernel.cache_dir%/hateoas')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
            ->arrayNode('serializer')
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('json')->defaultValue('hateoas.serializer.json_hal')->end()
                    ->scalarNode('xml')->defaultValue('hateoas.serializer.xml')->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
