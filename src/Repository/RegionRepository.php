<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Repository;

use Siganushka\GenericBundle\Repository\GenericEntityRepository;
use Siganushka\RegionBundle\Entity\Region;

/**
 * @extends GenericEntityRepository<Region>
 *
 * @method Region      createNew(...$args)
 * @method Region|null find($id, $lockMode = null, $lockVersion = null)
 * @method Region|null findOneBy(array $criteria, array $orderBy = null)
 * @method Region[]    findAll()
 * @method Region[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RegionRepository extends GenericEntityRepository
{
}
