<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Siganushka\RegionBundle\DependencyInjection\Configuration;
use Siganushka\RegionBundle\Model\RegionInterface;
use Siganushka\RegionBundle\Tests\Fixtures\TestRegion;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;

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
        $config = ['region_class' => TestRegion::class];

        $processedConfig = $this->processor->processConfiguration($this->configuration, [$config]);
        static::assertSame($processedConfig, $config);
    }

    public function testRegionClassInvalidConfigurationException(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage(\sprintf('The value must be instanceof %s, "stdClass" given.', RegionInterface::class));

        $config = ['region_class' => \stdClass::class];

        $this->processor->processConfiguration($this->configuration, [$config]);
    }
}
