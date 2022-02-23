<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Siganushka\RegionBundle\Controller\RegionController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes) {
    $routes->add('siganushka_region_collection_get', '/regions')
        ->controller(RegionController::class)
        ->methods(['GET'])
    ;
};
