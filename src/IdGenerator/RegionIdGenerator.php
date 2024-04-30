<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\IdGenerator;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Id\AbstractIdGenerator;
use Siganushka\RegionBundle\Entity\Region;

class RegionIdGenerator extends AbstractIdGenerator
{
    public function generateId(EntityManagerInterface $em, $entity): ?string
    {
        return $entity instanceof Region
            ? $entity->getCode()
            : null;
    }
}
