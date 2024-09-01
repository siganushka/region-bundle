<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Siganushka\RegionBundle\DependencyInjection\Configuration;
use Siganushka\RegionBundle\Entity\Region;
use Siganushka\RegionBundle\Tests\Mock\FooRegion;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class ConfigurationTest extends TestCase
{
    private ConfigurationInterface $configuration;
    private Processor $processor;

    protected function setUp(): void
    {
        $this->configuration = new Configuration();
        $this->processor = new Processor();
    }

    public function testDefaultConfig(): void
    {
        $treeBuilder = $this->configuration->getConfigTreeBuilder();

        static::assertInstanceOf(ConfigurationInterface::class, $this->configuration);
        static::assertInstanceOf(TreeBuilder::class, $treeBuilder);

        $processedConfig = $this->processor->processConfiguration($this->configuration, []);
        static::assertSame($processedConfig, ['region_class' => Region::class]);
    }

    public function testCustomConfig(): void
    {
        $config = ['region_class' => FooRegion::class];

        $processedConfig = $this->processor->processConfiguration($this->configuration, [$config]);
        static::assertSame($processedConfig, $config);
    }

    public function testRegionClassInvalidConfigurationException(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage(\sprintf('The value must be instanceof %s, "stdClass" given.', Region::class));

        $config = ['region_class' => \stdClass::class];
        $this->processor->processConfiguration($this->configuration, [$config]);
    }
}
