<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Siganushka\RegionBundle\Command\RegionUpdateCommand;
use Siganushka\RegionBundle\Controller\RegionController;
use Siganushka\RegionBundle\DependencyInjection\SiganushkaRegionExtension;
use Siganushka\RegionBundle\Form\Type\RegionCityType;
use Siganushka\RegionBundle\Form\Type\RegionDistrictType;
use Siganushka\RegionBundle\Form\Type\RegionProvinceType;
use Siganushka\RegionBundle\Form\Type\RegionSubjectType;
use Siganushka\RegionBundle\Serializer\Normalizer\RegionNormalizer;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @internal
 * @coversNothing
 */
final class SiganushkaRegionExtensionTest extends TestCase
{
    public function testLoadDefaultConfig(): void
    {
        $container = $this->createContainer();
        $container->loadFromExtension('siganushka_region');
        $container->compile();

        static::assertTrue($container->has(RegionUpdateCommand::class));
        static::assertTrue($container->has(RegionController::class));
        static::assertTrue($container->has(RegionNormalizer::class));
        static::assertTrue($container->has(RegionProvinceType::class));
        static::assertTrue($container->has(RegionCityType::class));
        static::assertTrue($container->has(RegionDistrictType::class));
        static::assertTrue($container->has(RegionSubjectType::class));
    }

    protected function createContainer()
    {
        $container = new ContainerBuilder();
        $container->registerExtension(new SiganushkaRegionExtension());

        $container->getCompilerPassConfig()->setRemovingPasses([]);
        $container->getCompilerPassConfig()->setAfterRemovingPasses([]);

        return $container;
    }
}
