<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Siganushka\RegionBundle\Controller\RegionController;
use Siganushka\RegionBundle\DependencyInjection\SiganushkaRegionExtension;
use Siganushka\RegionBundle\Tests\Mock\FooRegion;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class SiganushkaRegionExtensionTest extends TestCase
{
    public function testLoadDefaultConfig(): void
    {
        $container = $this->createContainerWithConfigs([]);

        static::assertTrue($container->hasDefinition(RegionController::class));
        static::assertTrue($container->hasDefinition('siganushka_region.command.region_update'));
        static::assertTrue($container->hasDefinition('siganushka_region.form.type.region_province'));
        static::assertTrue($container->hasDefinition('siganushka_region.form.type.region_city'));
        static::assertTrue($container->hasDefinition('siganushka_region.form.type.region_district'));
        static::assertTrue($container->hasDefinition('siganushka_region.form.type.region_subject'));
        static::assertFalse($container->hasDefinition('siganushka_region.doctrine.listener.entity_to_superclass'));
    }

    public function testLoadCustomConfig(): void
    {
        $container = $this->createContainerWithConfigs([
            ['region_class' => FooRegion::class],
        ]);

        $entityToSuperclassDef = $container->getDefinition('siganushka_region.doctrine.listener.entity_to_superclass');
        static::assertTrue($entityToSuperclassDef->hasTag('doctrine.event_listener'));

        $tag = $entityToSuperclassDef->getTag('doctrine.event_listener');
        static::assertContains('loadClassMetadata', array_column($tag, 'event'));
    }

    /**
     * @param array<mixed> $configs
     */
    protected function createContainerWithConfigs(array $configs): ContainerBuilder
    {
        $container = new ContainerBuilder();

        $extension = new SiganushkaRegionExtension();
        $extension->load($configs, $container);

        return $container;
    }
}
