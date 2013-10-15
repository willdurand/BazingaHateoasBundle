<?php

/**
 * This file is part of the HateoasBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Bazinga\Bundle\HateoasBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;

/**
 * @author William Durand <william.durand1@gmail.com>
 */
class BazingaHateoasExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        foreach (array('serializer.xml', 'configuration.xml', 'generator.xml') as $file) {
            $loader->load($file);
        }

        // Based on JMSSerializerBundle
        if ('none' === $config['metadata']['cache']) {
            $container->removeAlias('hateoas.configuration.metadata.cache');
        } elseif ('file' === $config['metadata']['cache']) {
            $container
                ->getDefinition('hateoas.configuration.metadata.cache.file_cache')
                ->replaceArgument(0, $config['metadata']['file_cache']['dir']);

            $dir = $container->getParameterBag()->resolveValue($config['metadata']['file_cache']['dir']);

            if (!file_exists($dir)) {
                if (!$rs = @mkdir($dir, 0777, true)) {
                    throw new RuntimeException(sprintf('Could not create cache directory "%s".', $dir));
                }
            }
        } else {
            $container->setAlias(
                'hateoas.configuration.metadata.cache',
                new Alias($config['metadata']['cache'], false)
            );
        }
    }
}
