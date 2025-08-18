<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Repository;

use Siganushka\GenericBundle\Repository\NestableRepository;
use Siganushka\RegionBundle\Entity\Region;

/**
 * @template T of Region = Region
 *
 * @extends NestableRepository<T>
 */
class RegionRepository extends NestableRepository
{
}
