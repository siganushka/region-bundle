<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\DependencyInjection;

use Siganushka\RegionBundle\Entity\AbstractRegion;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public const RESOURCE_MAPPING = [
        'region_class' => AbstractRegion::class,
    ];

    /**
     * @return TreeBuilder<'array'>
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('siganushka_region');
        $rootNode = $treeBuilder->getRootNode();

        foreach (self::RESOURCE_MAPPING as $configName => $abstractClass) {
            $rootNode->children()
                ->scalarNode($configName)
                    ->isRequired()
                    ->cannotBeEmpty()
                    ->validate()
                        ->ifTrue(static fn (mixed $v): bool => \is_string($v) && !is_subclass_of($v, $abstractClass, true))
                        ->thenInvalid('The value must be instanceof '.$abstractClass.', %s given.')
                    ->end()
                ->end()
            ;
        }

        return $treeBuilder;
    }
}
