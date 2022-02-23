<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\DependencyInjection;

use Siganushka\RegionBundle\Entity\Region;
use Siganushka\RegionBundle\Entity\RegionInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('siganushka_region');
        /** @var ArrayNodeDefinition */
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('region_class')
                    ->defaultValue(Region::class)
                    ->validate()
                    ->ifTrue(function ($regionClass) {
                        if (!class_exists($regionClass)) {
                            return false;
                        }

                        return !(new \ReflectionClass($regionClass))->implementsInterface(RegionInterface::class);
                    })
                    ->thenInvalid('The %s class must implement '.RegionInterface::class.' for using the "region_class".')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
