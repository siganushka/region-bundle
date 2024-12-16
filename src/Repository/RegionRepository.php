<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Repository;

use Siganushka\GenericBundle\Repository\GenericEntityRepository;
use Siganushka\RegionBundle\Entity\Region;

/**
 * @template T of Region = Region
 *
 * @extends GenericEntityRepository<T>
 */
class RegionRepository extends GenericEntityRepository
{
}
