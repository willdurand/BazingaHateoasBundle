<?php

declare(strict_types=1);

/**
 * This file is part of the HateoasBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bazinga\Bundle\HateoasBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AttributeDriverPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (PHP_VERSION_ID < 80100) {
            $container->removeDefinition('hateoas.configuration.metadata.attribute_driver');
        }
    }
}
