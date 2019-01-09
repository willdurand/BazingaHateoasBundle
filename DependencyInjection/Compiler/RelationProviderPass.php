<?php

declare(strict_types=1);

/**
 * This file is part of the HateoasBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bazinga\Bundle\HateoasBundle\DependencyInjection\Compiler;

use Hateoas\Configuration\Provider\RelationProviderInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

class RelationProviderPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $registryDefinition = $container->findDefinition('hateoas.configuration.provider');

        $relationProviderDefinitions = [];
        foreach (array_keys($container->findTaggedServiceIds('hateoas.relation_provider')) as $id) {
            $definition = $container->getDefinition($id);
            $this->assertProvider($container, $definition);
            $relationProviderDefinitions[] = $definition;
        }

        $registryDefinition->replaceArgument(0, $relationProviderDefinitions);
    }

    private function assertProvider(ContainerBuilder $container, Definition $definition): void
    {
        $class = $container->getParameterBag()->resolveValue($definition->getClass());
        $refClass = new \ReflectionClass($class);

        if (!$refClass->implementsInterface(RelationProviderInterface::class)) {
            throw new InvalidArgumentException(sprintf(
                'Relation provider "%s" does not implement the ReleationProviderResolver interface',
                $definition->getClass()
            ));
        }
    }
}
