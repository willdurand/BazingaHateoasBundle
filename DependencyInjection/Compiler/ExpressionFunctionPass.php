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
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class ExpressionFunctionPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $expressionEvaluator = $container->getDefinition('hateoas.expression.evaluator');

        foreach ($container->findTaggedServiceIds('hateoas.expression_function') as $id => $tags) {
            $functionDefinition = $container->getDefinition($id);

            if (!$this->implementsExpressionFunctionInterface($container, $functionDefinition)) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Service %s tagged with hateoas.expression_function must implement %s',
                        $id,
                        'Hateoas\Expression\ExpressionFunctionInterface'
                    )
                );
            }

            $expressionEvaluator->addMethodCall('registerFunctionId', array($id));
        }
    }

    private function implementsExpressionFunctionInterface(ContainerBuilder $container, Definition $definition)
    {
        $class = $container->getParameterBag()->resolveValue($definition->getClass());
        $refClass = new \ReflectionClass($class);

        return $refClass->implementsInterface('Hateoas\Expression\ExpressionFunctionInterface');
    }
}
