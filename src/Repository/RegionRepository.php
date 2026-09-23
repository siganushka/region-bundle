<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Repository;

use Doctrine\Common\Collections\Order;
use Doctrine\ORM\QueryBuilder;
use Siganushka\GenericBundle\Repository\NestableRepository;
use Siganushka\RegionBundle\Dto\RegionQueryDto;
use Siganushka\RegionBundle\Model\RegionInterface;

/**
 * @template T of RegionInterface = RegionInterface
 *
 * @extends NestableRepository<T>
 */
class RegionRepository extends NestableRepository
{
    public function createQueryBuilderFromDto(string $alias, RegionQueryDto $dto): QueryBuilder
    {
        $criteria = self::createCriteriaFromDto($dto);
        $criteria->orderBy(['parent' => Order::Ascending, 'code' => Order::Ascending]);
        $criteria->setMaxResults(100);

        $qb = $this->createQueryBuilder($alias);
        $qb->addCriteria($criteria);

        return $qb;
    }
}
