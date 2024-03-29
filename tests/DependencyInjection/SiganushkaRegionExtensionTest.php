<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Siganushka\RegionBundle\Controller\RegionController;
use Siganushka\RegionBundle\DependencyInjection\SiganushkaRegionExtension;
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
