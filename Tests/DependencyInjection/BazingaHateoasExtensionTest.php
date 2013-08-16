<?php

namespace Bazinga\Bundle\HateoasBundle\Tests\DependencyInjection;

use Bazinga\Bundle\HateoasBundle\BazingaHateoasBundle;
use Bazinga\Bundle\HateoasBundle\Tests\TestCase;
use Bazinga\Bundle\HateoasBundle\Tests\Fixtures\SimpleObject;
use Doctrine\Common\Annotations\AnnotationReader;
use JMS\SerializerBundle\JMSSerializerBundle;
use Symfony\Component\DependencyInjection\Compiler\ResolveParameterPlaceHoldersPass;
use Symfony\Component\DependencyInjection\Compiler\ResolveDefinitionTemplatesPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
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
        $container  = $this->getContainerForConfig(array(array()));
        $serializer = $container->get('serializer');

        $this->assertEquals(
            json_encode(array(
                'name' => 'hello',
                '_links' => array(
                    'self' => array('href' => 'http://somewhere/simple-objects'),
                )
            )),
            $serializer->serialize(new SimpleObject('hello'), 'json')
        );
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
        ));
        $container->getCompilerPassConfig()->setRemovingPasses(array());
        $container->compile();

        return $container;
    }
}
