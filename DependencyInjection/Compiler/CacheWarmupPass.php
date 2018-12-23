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
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class CacheWarmupPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        try {
            $warmupService = clone $container->findDefinition('jms_serializer.cache.cache_warmer');

            $warmupService->setArgument(1, $container->findDefinition('hateoas.configuration.metadata_factory'));
            $container->setDefinition('hateoas.configuration.metadata.cache.cache_warmer', $warmupService);
        } catch (ServiceNotFoundException $exception) {
        }
    }
}
