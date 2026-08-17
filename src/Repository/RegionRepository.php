<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Repository;

use Doctrine\Common\Collections\Criteria;
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
    public function createQueryBuilderByDto(string $alias, RegionQueryDto $dto): QueryBuilder
    {
        $criteria = new Criteria(firstResult: 0, accessRawFieldValues: true);
        $criteria->orderBy(['parent' => Order::Ascending, 'code' => Order::Ascending]);

        if ($dto->parent || !$dto->name) {
            $criteria->andWhere(Criteria::expr()->eq('parent', $dto->parent));
        }

        if ($dto->name) {
            $criteria->andWhere(Criteria::expr()->contains('name', $dto->name));
        }

        $qb = $this->createQueryBuilder($alias);
        $qb->addCriteria($criteria);

        return $qb;
    }
}
