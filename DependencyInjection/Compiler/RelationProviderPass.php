<?php

/**
 * This file is part of the HateoasBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Bazinga\Bundle\HateoasBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

/**
 * @author Daniel Leech <daniel@dantleech.com>
 */
class RelationProviderPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $registryDefinition = $container->getDefinition('hateoas.configuration.provider.resolver.chain');

        $relationProviderDefinitions = array();
        foreach (array_keys($container->findTaggedServiceIds('hateoas.relation_provider')) as $id) {
            $definition = $container->getDefinition($id);
            $this->assertProvider($container, $definition);
            $relationProviderDefinitions[] = $definition;
        }

        $registryDefinition->replaceArgument(0, $relationProviderDefinitions);
    }

    private function assertProvider(ContainerBuilder $container, Definition $definition)
    {
        $class = $container->getParameterBag()->resolveValue($definition->getClass());
        $refClass = new \ReflectionClass($class);

        if (!$refClass->implementsInterface('Hateoas\Configuration\Provider\Resolver\RelationProviderResolverInterface')) {
            throw new InvalidArgumentException(sprintf(
                'Relation provider "%s" does not implement the ReleationProviderResolver interface',
                $definition->getClass()
            ));
        }
    }
}
