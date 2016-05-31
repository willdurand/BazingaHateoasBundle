<?php

/**
 * This file is part of the HateoasBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Bazinga\Bundle\HateoasBundle\DependencyInjection\Compiler;

use Hateoas\Configuration\Metadata\ConfigurationExtensionInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

class ExtensionDriverPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $extensionDriver = $container->getDefinition('hateoas.configuration.metadata.extension_driver');

        foreach ($container->findTaggedServiceIds('hateoas.configuration_extension') as $id => $tags) {
            $extensionDefinition = $container->getDefinition($id);

            if (!$this->implementsConfigurationExtensionInterface($container, $extensionDefinition)) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Service %s tagged with hateoas.configuration_extension must implement %s',
                        $id,
                        ConfigurationExtensionInterface::class
                    )
                );
            }

            $extensionDriver->addMethodCall('registerExtension', [$extensionDefinition]);
        }
    }

    private function implementsConfigurationExtensionInterface(ContainerBuilder $container, Definition $definition)
    {
        $class = $container->getParameterBag()->resolveValue($definition->getClass());
        $refClass = new \ReflectionClass($class);

        return $refClass->implementsInterface(ConfigurationExtensionInterface::class);
    }
}
