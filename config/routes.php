<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Siganushka\RegionBundle\Controller\RegionController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes): void {
    $routes->add('siganushka_region_getcollection', '/regions')
        ->controller([RegionController::class, 'getCollection'])
        ->methods(['GET'])
    ;

    $routes->add('siganushka_region_getitem', '/regions/{code<\d{2,9}>}')
        ->controller([RegionController::class, 'getItem'])
        ->methods(['GET'])
    ;
};
