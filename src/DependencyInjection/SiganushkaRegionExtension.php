<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\DependencyInjection;

use Siganushka\RegionBundle\Entity\Region;
use Siganushka\RegionBundle\Repository\RegionRepository;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

class SiganushkaRegionExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new PhpFileLoader($container, new FileLocator(\dirname(__DIR__).'/Resources/config'));
        $loader->load('services.php');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $regionRepositoryDef = $container->findDefinition(RegionRepository::class);
        $regionRepositoryDef->setArgument('$entityClass', $config['region_class']);
    }

    public function prepend(ContainerBuilder $container): void
    {
        if (!$container->hasExtension('siganushka_region')) {
            return;
        }

        $configs = $container->getExtensionConfig($this->getAlias());

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $overrideMappings = [];
        if (Region::class !== $config['region_class']) {
            $overrideMappings[] = Region::class;
        }

        $container->prependExtensionConfig('siganushka_generic', [
            'doctrine' => ['entity_to_superclass' => $overrideMappings],
        ]);
    }
}
