<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Siganushka\RegionBundle\Command\RegionUpdateCommand;
use Siganushka\RegionBundle\Controller\RegionController;
use Siganushka\RegionBundle\DependencyInjection\SiganushkaRegionExtension;
use Siganushka\RegionBundle\Doctrine\ORM\Id\RegionCodeGenerator;
use Siganushka\RegionBundle\Form\Extension\RegionTypeExtension;
use Siganushka\RegionBundle\Form\Type\RegionType;
use Siganushka\RegionBundle\Model\RegionInterface;
use Siganushka\RegionBundle\Repository\RegionRepository;
use Siganushka\RegionBundle\Tests\Fixtures\TestRegion;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class SiganushkaRegionExtensionTest extends TestCase
{
    public function testLoad(): void
    {
        $container = new ContainerBuilder();

        $extension = new SiganushkaRegionExtension();
        $extension->load([['region_class' => TestRegion::class]], $container);

        static::assertTrue($container->hasDefinition(RegionUpdateCommand::class));
        static::assertTrue($container->hasDefinition(RegionController::class));
        static::assertTrue($container->hasDefinition(RegionCodeGenerator::class));
        static::assertTrue($container->hasDefinition(RegionTypeExtension::class));
        static::assertTrue($container->hasDefinition(RegionType::class));
        static::assertTrue($container->hasDefinition(RegionRepository::class));
    }

    public function testPrepend(): void
    {
        $container = new ContainerBuilder();

        $extension = new SiganushkaRegionExtension();
        $extension->prepend($container);

        static::assertSame([], $container->getExtensionConfig('doctrine'));

        $container->prependExtensionConfig('siganushka_region', ['region_class' => 'foo']);
        $extension->prepend($container);

        static::assertSame([
            [
                'orm' => [
                    'resolve_target_entities' => [RegionInterface::class => 'foo'],
                ],
            ],
        ], $container->getExtensionConfig('doctrine'));
    }
}
