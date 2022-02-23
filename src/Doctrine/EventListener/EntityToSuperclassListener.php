<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Doctrine\EventListener;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Siganushka\RegionBundle\Entity\Region;

class EntityToSuperclassListener
{
    public function loadClassMetadata(LoadClassMetadataEventArgs $event): void
    {
        $classMetadata = $event->getClassMetadata();
        if (Region::class === $classMetadata->getName()) {
            $classMetadata->isMappedSuperclass = true;
        }
    }
}
