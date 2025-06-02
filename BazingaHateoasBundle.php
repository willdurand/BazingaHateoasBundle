<?php

/**
 * This file is part of the HateoasBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Bazinga\Bundle\HateoasBundle;

use Bazinga\Bundle\HateoasBundle\DependencyInjection\Compiler\AttributeDriverPass;
use Bazinga\Bundle\HateoasBundle\DependencyInjection\Compiler\CacheWarmupPass;
use Bazinga\Bundle\HateoasBundle\DependencyInjection\Compiler\ExtensionDriverPass;
use Bazinga\Bundle\HateoasBundle\DependencyInjection\Compiler\RelationProviderPass;
use Bazinga\Bundle\HateoasBundle\DependencyInjection\Compiler\UrlGeneratorPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class BazingaHateoasBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new UrlGeneratorPass());
        $container->addCompilerPass(new RelationProviderPass());
        $container->addCompilerPass(new AttributeDriverPass());
        $container->addCompilerPass(new ExtensionDriverPass());
        $container->addCompilerPass(new CacheWarmupPass());
    }
}
