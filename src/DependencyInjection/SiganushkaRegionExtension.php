<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\DependencyInjection;

use Doctrine\ORM\Mapping\MappedSuperclass;
use Symfony\Component\AssetMapper\AssetMapperInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

class SiganushkaRegionExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new PhpFileLoader($container, new FileLocator(__DIR__.'/../../config'));
        $loader->load('services.php');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        foreach (Configuration::RESOURCE_MAPPING as $configName => $abstractClass) {
            $ref = new \ReflectionClass($abstractClass);
            if ($repositoryClass = ($ref->getAttributes(MappedSuperclass::class)[0]->getArguments()['repositoryClass'] ?? null)) {
                $repository = $container->findDefinition($repositoryClass);
                $repository->setArgument('$entityClass', $config[$configName]);
            }
        }
    }

    public function prepend(ContainerBuilder $container): void
    {
        $configs = $container->getExtensionConfig($this->getAlias());
        $config = array_merge(...$configs);

        $resolveTargetEntities = [];
        foreach (Configuration::RESOURCE_MAPPING as $configName => $abstractClass) {
            $resolveTargetEntities[$abstractClass] = $config[$configName] ?? null;
        }

        if (\count($r = array_filter($resolveTargetEntities))) {
            $container->prependExtensionConfig('doctrine', [
                'orm' => ['resolve_target_entities' => $r],
            ]);
        }

        if ($this->isAssetMapperAvailable($container)) {
            $container->prependExtensionConfig('framework', [
                'asset_mapper' => [
                    'paths' => [__DIR__.'/../../assets/dist' => '@siganushka/region-bundle'],
                ],
            ]);
        }
    }

    private function isAssetMapperAvailable(ContainerBuilder $container): bool
    {
        if (!interface_exists(AssetMapperInterface::class)) {
            return false;
        }

        $bundlesMetadata = $container->getParameter('kernel.bundles_metadata');
        if (!isset($bundlesMetadata['FrameworkBundle'])) {
            return false;
        }

        return is_file($bundlesMetadata['FrameworkBundle']['path'].'/Resources/config/asset_mapper.php');
    }
}
