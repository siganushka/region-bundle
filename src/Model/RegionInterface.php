<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Model;

use Siganushka\GenericBundle\Model\NestableInterface;

/**
 * @extends NestableInterface<RegionInterface>
 */
interface RegionInterface extends NestableInterface
{
    public function getCode(): string;

    public function getName(): string;
}
