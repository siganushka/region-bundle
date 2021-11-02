<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Siganushka\RegionBundle\Command\RegionUpdateCommand;
use Siganushka\RegionBundle\Controller\RegionController;
use Siganushka\RegionBundle\Form\Type\RegionCityType;
use Siganushka\RegionBundle\Form\Type\RegionDistrictType;
use Siganushka\RegionBundle\Form\Type\RegionProvinceType;
use Siganushka\RegionBundle\Form\Type\RegionSubjectType;
use Siganushka\RegionBundle\Serializer\Normalizer\RegionNormalizer;

return static function (ContainerConfigurator $container) {
    $service = function (string $serviceId) {
        return \function_exists('Symfony\Component\DependencyInjection\Loader\Configurator\service')
            ? service($serviceId)
            : ref($serviceId);
    };

    $container->services()
        ->set(RegionController::class)
            ->arg(0, $service('event_dispatcher'))
            ->arg(1, $service('doctrine'))
            ->arg(2, $service('serializer'))
            ->tag('controller.service_arguments')

        ->set('siganushka_region.command.region_update', RegionUpdateCommand::class)
            ->arg(0, $service('doctrine'))
            ->tag('console.command')

        ->set('siganushka_region.serializer.normalizer.region', RegionNormalizer::class)
            ->tag('serializer.normalizer')

        ->set('siganushka_region.form.type.region_subject', RegionSubjectType::class)
            ->tag('form.type')

        ->set('siganushka_region.form.type.region_province', RegionProvinceType::class)
            ->arg(0, $service('doctrine'))
            ->tag('form.type')

        ->set('siganushka_region.form.type.region_city', RegionCityType::class)
            ->tag('form.type')

        ->set('siganushka_region.form.type.region_district', RegionDistrictType::class)
            ->tag('form.type')
    ;
};
