<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Siganushka\RegionBundle\Command\RegionUpdateCommand;
use Siganushka\RegionBundle\Controller\RegionController;
use Siganushka\RegionBundle\DependencyInjection\SiganushkaRegionExtension;
use Siganushka\RegionBundle\Entity\Region;
use Siganushka\RegionBundle\Form\Type\RegionType;
use Siganushka\RegionBundle\Repository\RegionRepository;
use Siganushka\RegionBundle\Tests\Fixtures\FooRegion;
use Symfony\Component\DependencyInjection\Compiler\ResolveChildDefinitionsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class SiganushkaRegionExtensionTest extends TestCase
{
    public function testDefaultConfig(): void
    {
        $container = $this->createContainerWithConfig();
        // dd($container->getServiceIds(), $container->getAliases());

        static::assertTrue($container->hasDefinition(RegionUpdateCommand::class));
        static::assertTrue($container->hasDefinition(RegionController::class));
        static::assertTrue($container->hasDefinition(RegionType::class));
        static::assertTrue($container->hasDefinition(RegionRepository::class));

        $regionRepositoryDef = $container->getDefinition(RegionRepository::class);
        static::assertSame(Region::class, $regionRepositoryDef->getArgument('$entityClass'));
    }

    public function testCustomRegionClass(): void
    {
        $container = $this->createContainerWithConfig(['region_class' => FooRegion::class]);

        $regionRepositoryDef = $container->getDefinition(RegionRepository::class);
        static::assertSame(FooRegion::class, $regionRepositoryDef->getArgument('$entityClass'));
    }

    private function createContainerWithConfig(array $config = []): ContainerBuilder
    {
        $extension = new SiganushkaRegionExtension();

        $container = new ContainerBuilder();
        $container->registerExtension($extension);
        $container->loadFromExtension($extension->getAlias(), $config);

        $container->getCompilerPassConfig()->setOptimizationPasses([new ResolveChildDefinitionsPass()]);
        $container->getCompilerPassConfig()->setRemovingPasses([]);
        $container->getCompilerPassConfig()->setAfterRemovingPasses([]);
        $container->compile();

        return $container;
    }
}
