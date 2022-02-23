<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\DependencyInjection;

use Siganushka\RegionBundle\Entity\Region;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class SiganushkaRegionExtension extends Extension
{
    /**
     * @param array<mixed> $configs
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new PhpFileLoader($container, new FileLocator(\dirname(__DIR__).'/Resources/config'));
        $loader->load('services.php');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('siganushka_region.region_class', $config['region_class']);

        if (Region::class === $config['region_class']) {
            $container->removeDefinition('siganushka_region.doctrine.listener.entity_to_superclass');
        }
    }
}
