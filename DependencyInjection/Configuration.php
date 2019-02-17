<?php

declare(strict_types=1);

/**
 * This file is part of the HateoasBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bazinga\Bundle\HateoasBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('bazinga_hateoas');
        if (\method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            // BC layer for symfony/config 4.1 and older
            $rootNode = $treeBuilder->root('bazinga_hateoas');
        }

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
            ->arrayNode('twig_extension')
                ->addDefaultsIfNotSet()
                ->canBeDisabled()
            ->end();

        return $treeBuilder;
    }
}
