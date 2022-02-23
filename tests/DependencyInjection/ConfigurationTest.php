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

class ConfigurationTest extends TestCase
{
    private ?ConfigurationInterface $configuration = null;
    private ?Processor $processor = null;

    protected function setUp(): void
    {
        $this->configuration = new Configuration();
        $this->processor = new Processor();
    }

    protected function tearDown(): void
    {
        $this->configuration = null;
        $this->processor = null;
    }

    public function testDefaultConfig(): void
    {
        static::assertInstanceOf(ConfigurationInterface::class, $this->configuration);
        static::assertInstanceOf(TreeBuilder::class, $this->configuration->getConfigTreeBuilder());

        $processedConfig = $this->processor->processConfiguration($this->configuration, []);

        static::assertSame($processedConfig, [
            'region_class' => Region::class,
        ]);
    }

    public function testCustomConfig(): void
    {
        $processedConfig = $this->processor->processConfiguration($this->configuration, [
            ['region_class' => FooRegion::class],
        ]);

        static::assertSame($processedConfig, [
            'region_class' => FooRegion::class,
        ]);
    }

    public function testInvalidRegionClassException(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Invalid configuration for path "siganushka_region.region_class"');

        $this->processor->processConfiguration($this->configuration, [
            ['region_class' => \stdClass::class],
        ]);
    }
}
