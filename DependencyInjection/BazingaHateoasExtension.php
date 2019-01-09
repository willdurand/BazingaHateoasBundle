<?php

declare(strict_types=1);

/**
 * This file is part of the HateoasBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bazinga\Bundle\HateoasBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class BazingaHateoasExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration(new Configuration(), $configs);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        foreach (['serializer', 'configuration', 'generator', 'helper', 'twig'] as $file) {
            if ('twig' === $file && false === $config['twig_extension']['enabled']) {
                continue;
            }

            $loader->load($file . '.xml');
        }

        // Based on JMSSerializerBundle
        if ('none' === $config['metadata']['cache']) {
            $container->removeAlias('hateoas.configuration.metadata.cache');
        } elseif ('file' === $config['metadata']['cache']) {
            $container
                ->getDefinition('hateoas.configuration.metadata.cache.file_cache')
                ->replaceArgument(0, $config['metadata']['file_cache']['dir']);

            $dir = $container->getParameterBag()->resolveValue($config['metadata']['file_cache']['dir']);
            if (!is_dir($dir) && !@mkdir($dir, 0777, true) && !is_dir($dir)) {
                throw new \RuntimeException(sprintf('Could not create cache directory "%s".', $dir));
            }
        } else {
            $container->setAlias(
                'hateoas.configuration.metadata.cache',
                new Alias($config['metadata']['cache'], false)
            );
        }

        $container
            ->getDefinition('hateoas.event_listener.json')
            ->setPublic(true)
            ->replaceArgument(0, new Reference($config['serializer']['json']));

        $container
            ->getDefinition('hateoas.event_listener.xml')
            ->setPublic(true)
            ->replaceArgument(0, new Reference($config['serializer']['xml']));
    }
}
