<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\DependencyInjection;

use Siganushka\RegionBundle\Model\RegionInterface;
use Siganushka\RegionBundle\Repository\RegionRepository;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public const RESOURCE_MAPPING = [
        'region_class' => [RegionInterface::class, RegionRepository::class],
    ];

    /**
     * @return TreeBuilder<'array'>
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('siganushka_region');
        $rootNode = $treeBuilder->getRootNode();

        foreach (self::RESOURCE_MAPPING as $configName => [$interface]) {
            $rootNode->children()
                ->scalarNode($configName)
                    ->isRequired()
                    ->cannotBeEmpty()
                    ->validate()
                        ->ifTrue(static fn (mixed $v): bool => \is_string($v) && !is_subclass_of($v, $interface, true))
                        ->thenInvalid('The value must be instanceof '.$interface.', %s given.')
                    ->end()
                ->end()
            ;
        }

        return $treeBuilder;
    }
}
