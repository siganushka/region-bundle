<?php

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

class SiganushkaRegionExtensionTest extends TestCase
{
    public function testLoadDefaultConfig()
    {
        $container = $this->createContainer();
        $container->loadFromExtension('siganushka_region');
        $container->compile();

        $this->assertTrue($container->has(RegionUpdateCommand::class));
        $this->assertTrue($container->has(RegionController::class));
        $this->assertTrue($container->has(RegionNormalizer::class));
        $this->assertTrue($container->has(RegionProvinceType::class));
        $this->assertTrue($container->has(RegionCityType::class));
        $this->assertTrue($container->has(RegionDistrictType::class));
        $this->assertTrue($container->has(RegionSubjectType::class));
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
