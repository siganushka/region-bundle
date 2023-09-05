<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Tests\Event;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Siganushka\RegionBundle\Entity\Region;
use Siganushka\RegionBundle\Entity\RegionInterface;
use Siganushka\RegionBundle\Event\RegionFilterEvent;

final class RegionFilterEventTest extends TestCase
{
    public function testRegionFilterEvent(): void
    {
        $region = new Region();
        $region->setCode('001');
        $region->setName('foo1');

        $array = [$region];
        $arrayCollection = new ArrayCollection($array);

        $event1 = new RegionFilterEvent($array);
        $event2 = new RegionFilterEvent($arrayCollection);

        static::assertSame($array, $event1->getRegions());
        static::assertSame($array, $event2->getRegions());
    }

    /**
     * @psalm-suppress InvalidArgument
     */
    public function testRegionFilterEventException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('Array of regions must be type of %s', RegionInterface::class));

        new RegionFilterEvent([new \stdClass()]);
    }
}
