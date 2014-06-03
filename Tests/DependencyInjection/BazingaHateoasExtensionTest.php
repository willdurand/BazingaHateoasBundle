<?php

namespace Bazinga\Bundle\HateoasBundle\Tests\DependencyInjection;

use Bazinga\Bundle\HateoasBundle\BazingaHateoasBundle;
use Bazinga\Bundle\HateoasBundle\DependencyInjection\Compiler\UrlGeneratorPass;
use Bazinga\Bundle\HateoasBundle\Tests\TestCase;
use Bazinga\Bundle\HateoasBundle\Tests\Fixtures\SimpleObject;
use Doctrine\Common\Annotations\AnnotationReader;
use JMS\SerializerBundle\JMSSerializerBundle;
use Symfony\Component\DependencyInjection\Compiler\ResolveParameterPlaceHoldersPass;
use Symfony\Component\DependencyInjection\Compiler\ResolveDefinitionTemplatesPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\KernelInterface;

class BazingaHateoasExtensionTest extends TestCase
{
    public function setUp()
    {
        $this->clearTempDir();
    }

    public function tearDown()
    {
        $this->clearTempDir();
    }

    public function testLoad()
    {
        $container = $this->getContainerForConfig(array(array()));
        $container->compile();

        $serializer = $container->get('serializer');

        $this->assertEquals(
            json_encode(array(
                'name' => 'hello',
                '_links' => array(
                    'all' => array(
                        'href' => 'http://somewhere/simple-objects',
                        'foo'  => 'bar',
                    ),
                )
            )),
            $serializer->serialize(new SimpleObject('hello'), 'json')
        );
    }

    public function testLoadUrlGenerator()
    {
        $container = $this->getContainerForConfig(array(array()));

        $urlGeneratorClass = 'Bazinga\Bundle\HateoasBundle\Tests\Fixtures\UrlGenerator';
        $container->setParameter('url_generator_2.class', $urlGeneratorClass);

        $this->registerUrlGenerator($container, 'url_generator_1', $urlGeneratorClass);
        $this->registerUrlGenerator($container, 'url_generator_2', '%url_generator_2.class%');

        $container->compile();

        $urlGeneratorRegistry = $container->get('hateoas.generator.registry');

        $urlGenerator1 = $urlGeneratorRegistry->get('url_generator_1');
        $this->assertInstanceOf($urlGeneratorClass, $urlGenerator1);

        $urlGenerator2 = $urlGeneratorRegistry->get('url_generator_2');
        $this->assertInstanceOf($urlGeneratorClass, $urlGenerator2);
    }

    private function clearTempDir()
    {
        // clear temporary directory
        $dir = $this->getTempDir();
        if (is_dir($dir)) {
            foreach (new \RecursiveDirectoryIterator($dir) as $file) {
                $filename = $file->getFileName();
                if ('.' === $filename || '..' === $filename) {
                    continue;
                }

                @unlink($file->getPathName());
            }

            @rmdir($dir);
        }
    }

    private function getTempDir()
    {
        return sys_get_temp_dir() . '/hateoas-bundle';
    }

    /**
     * @see https://github.com/schmittjoh/JMSSerializerBundle/blob/master/Tests/DependencyInjection/JMSSerializerExtensionTest.php
     */
    private function getContainerForConfig(array $configs, KernelInterface $kernel = null)
    {
        if (null === $kernel) {
            $kernel = $this->getMock('Symfony\Component\HttpKernel\KernelInterface');
            $kernel
                ->expects($this->any())
                ->method('getBundles')
                ->will($this->returnValue(array()))
                ;
        }

        $router  = $this->getMock('Symfony\Component\Routing\Generator\UrlGeneratorInterface');
        $bundles = array(
            new BazingaHateoasBundle($kernel),
            new JMSSerializerBundle($kernel),
        );

        $extensions = array_map(function ($bundle) {
            return $bundle->getContainerExtension();
        }, $bundles);

        $container = new ContainerBuilder();
        $container->setParameter('kernel.debug', true);
        $container->setParameter('kernel.cache_dir', $this->getTempDir());
        $container->setParameter('kernel.bundles', array());
        $container->set('annotation_reader', new AnnotationReader());
        $container->set('router', $router);
        $container->set('service_container', $container);
        $container->set('debug.stopwatch', $this->getMock('Symfony\Component\Stopwatch\Stopwatch'));

        $container->setParameter('foo', 'bar');

        foreach ($extensions as $extension) {
            $container->registerExtension($extension);
            $extension->load($configs, $container);
        }

        foreach ($bundles as $bundle) {
            $bundle->build($container);
        }

        $container->getCompilerPassConfig()->setOptimizationPasses(array(
            new ResolveParameterPlaceHoldersPass(),
            new ResolveDefinitionTemplatesPass(),
            new UrlGeneratorPass(),
        ));
        $container->getCompilerPassConfig()->setRemovingPasses(array());

        return $container;
    }

    private function registerUrlGenerator(ContainerBuilder $container, $id, $class)
    {
        $urlGeneratorDefinition = new Definition($class);
        $urlGeneratorDefinition->addTag('hateoas.url_generator');

        $container->setDefinition($id, $urlGeneratorDefinition);
    }
}
