<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Siganushka\RegionBundle\SiganushkaRegionBundle;

return static function (ContainerConfigurator $container): void {
    $services = $container->services()
        ->defaults()
            ->autowire()
            ->autoconfigure()
    ;

    $ref = new \ReflectionClass(SiganushkaRegionBundle::class);
    $services->load($ref->getNamespaceName().'\\', '../../')
        ->exclude([
            '../../DependencyInjection/',
            '../../Entity/',
            '../../Event/',
            '../../IdGenerator/',
            '../../Resources/',
            '../../SiganushkaRegionBundle.php',
        ]);
};
