<?php

declare(strict_types=1);

/**
 * This file is part of the HateoasBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bazinga\Bundle\HateoasBundle\DependencyInjection\Compiler;

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AttributeDriverPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!class_exists(AnnotationReader::class)) {
            $container->removeDefinition('hateoas.configuration.metadata.annotation_reader');
            $container->removeDefinition('hateoas.configuration.metadata.annotation_driver');
        }
    }
}
