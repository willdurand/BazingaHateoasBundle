<?php

declare(strict_types=1);

namespace Bazinga\Bundle\HateoasBundle\Tests\DependencyInjection;

use Bazinga\Bundle\HateoasBundle\BazingaHateoasBundle;
use Bazinga\Bundle\HateoasBundle\Tests\Fixtures\SimpleObject;
use Doctrine\Common\Annotations\AnnotationReader;
use JMS\SerializerBundle\JMSSerializerBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Stopwatch\Stopwatch;

class BazingaHateoasExtensionTest extends TestCase
{
    public function setUp(): void
    {
        $this->clearTempDir();
    }

    public function tearDown(): void
    {
        $this->clearTempDir();
    }

    public function testLoad()
    {
        $container = $this->getContainerForConfig([[]]);
        $container->compile();

        $serializer = $container->get('jms_serializer');

        $this->assertEquals(
            json_encode([
                'name' => 'hello',
                '_links' => [
                    'all' => [
                        'href' => 'http://somewhere/simple-objects',
                        'foo'  => 'bar',
                    ],
                    'all_2' => ['href' => 'http://somewhere/simple-objects'],
                ],
                '_embedded' => [
                    'e1' => '1',
                    'e2' => 2.0,
                ],
            ], JSON_PRESERVE_ZERO_FRACTION),
            $serializer->serialize(new SimpleObject('hello'), 'json')
        );
    }

    public function testRelationProviderPassInvalidProvider()
    {
        $container = $this->getContainerForConfig([[]]);
        $container->compile();
        $definition = $container->getDefinition('hateoas.configuration.provider.chain');
        $arguments = $definition->getArguments();
        $this->assertCount(1, $arguments);
        $this->assertCount(3, $arguments[0]);
    }

    public function testRelationProviderPass()
    {
        $this->expectException(InvalidArgumentException::class);

        $container = $this->getContainerForConfig([[]]);
        $definition = $container->register('invalid_relation_provider', 'stdClass');
        $definition->addTag('hateoas.relation_provider');
        $container->compile();
    }

    public function testLoadUrlGenerator()
    {
        $container = $this->getContainerForConfig([[]]);

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

    public function testLoadSerializer()
    {
        $class = 'Bazinga\Bundle\HateoasBundle\Tests\Fixtures\JsonSerializer';
        $container = $this->getContainerForConfig(['bazinga_hateoas' => ['serializer' => ['json' => 'custom_serializer']]]);
        $container->setDefinition('custom_serializer', new Definition($class));
        $container->compile();

        $jsonListener = $container->get('hateoas.event_listener.json');

        $reflClass = new \ReflectionClass($jsonListener);
        $reflProp = $reflClass->getProperty('serializer');
        $reflProp->setAccessible(true);

        $this->assertInstanceOf($class, $reflProp->getValue($jsonListener));

        $xmlListener = $container->get('hateoas.event_listener.xml');

        $reflClass = new \ReflectionClass($xmlListener);
        $reflProp = $reflClass->getProperty('serializer');
        $reflProp->setAccessible(true);

        $this->assertInstanceOf('Hateoas\Serializer\XmlSerializer', $reflProp->getValue($xmlListener));
    }

    public function testNotLoadingTwigHelper()
    {
        $this->expectException(InvalidArgumentException::class);

        $container = $this->getContainerForConfig(['bazinga_hateoas' => ['twig_extension' => ['enabled' => false]]]);
        $container->findDefinition('hateoas.twig.link');
        $container->compile();
    }

    public function testLoadInvalidConfigurationExtension()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Service invalid_configuration_extension tagged with hateoas.configuration_extension must implement Hateoas\Configuration\Metadata\ConfigurationExtensionInterface');

        $container = $this->getContainerForConfig([[]]);
        $container
            ->setDefinition(
                'invalid_configuration_extension',
                new Definition('stdClass')
            )
            ->addTag('hateoas.configuration_extension');
        $container->compile();
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
    private function getContainerForConfig(array $configs, ?KernelInterface $kernel = null)
    {
        if (null === $kernel) {
            $kernel = $this->createMock(KernelInterface::class);
            $kernel
                ->expects($this->any())
                ->method('getBundles')
                ->will($this->returnValue([]));
        }

        $router  = $this->createMock(UrlGeneratorInterface::class);
        $bundles = [
            new BazingaHateoasBundle($kernel),
            new JMSSerializerBundle($kernel),
        ];

        $extensions = array_map(function ($bundle) {
            return $bundle->getContainerExtension();
        }, $bundles);

        $container = new ContainerBuilder();
        $container->setParameter('kernel.debug', true);
        $container->setParameter('kernel.cache_dir', $this->getTempDir());
        $container->setParameter('kernel.bundles', []);
        $container->set('annotation_reader', new AnnotationReader());
        $container->setDefinition('doctrine', new Definition(Registry::class));
        $container->setDefinition('doctrine_phpcr', new Definition(Registry::class));
        $container->set('router', $router);
        $container->set('debug.stopwatch', $this->createMock(Stopwatch::class));

        $container->setParameter('foo', 'bar');

        foreach ($extensions as $extension) {
            $extensionConfig = $configs[$extension->getAlias()] ?? [];

            $container->registerExtension($extension);
            $extension->load([$extension->getAlias() => $extensionConfig], $container);
        }

        foreach ($bundles as $bundle) {
            $bundle->build($container);
        }

        $container->getDefinition('hateoas.configuration.provider.chain')
            ->setPublic(true);

        return $container;
    }

    private function registerUrlGenerator(ContainerBuilder $container, string $id, string $class)
    {
        $urlGeneratorDefinition = new Definition($class);
        $urlGeneratorDefinition->addTag('hateoas.url_generator');

        $container->setDefinition($id, $urlGeneratorDefinition);
    }
}
