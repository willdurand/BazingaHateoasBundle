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

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class UrlGeneratorPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $registryDefinition = $container->getDefinition('hateoas.generator.registry');

        foreach ($container->findTaggedServiceIds('hateoas.url_generator') as $id => $attributes) {
            $name = !empty($attributes[0]['alias']) ? $attributes[0]['alias'] : $id;

            if ($this->isSymfonyUrlGenerator($container->getDefinition($id))) {
                $definition = new Definition(
                    'Hateoas\UrlGenerator\SymfonyUrlGenerator',
                    array(new Reference($id))
                );
                $definition->setPublic(false);

                $id = 'hateoas.generator.user.' . $name;

                $container->setDefinition($id, $definition);
            }

            $registryDefinition->addMethodCall(
                'set',
                array($name, new Reference($id))
            );
        }
    }

    private function isSymfonyUrlGenerator(Definition $definition)
    {
        $refClass = new \ReflectionClass($definition->getClass());

        return $refClass->implementsInterface('Symfony\Component\Routing\Generator\UrlGeneratorInterface');
    }
}
