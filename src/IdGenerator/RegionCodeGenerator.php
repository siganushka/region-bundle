<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\IdGenerator;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Id\AbstractIdGenerator;
use Siganushka\RegionBundle\Entity\Region;

class RegionCodeGenerator extends AbstractIdGenerator
{
    public function generateId(EntityManagerInterface $em, ?object $entity): ?string
    {
        return $entity instanceof Region
            ? $entity->getCode()
            : null;
    }
}
