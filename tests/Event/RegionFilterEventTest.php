<?php

namespace Siganushka\RegionBundle\Tests\Event;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Siganushka\RegionBundle\Entity\Region;
use Siganushka\RegionBundle\Entity\RegionInterface;
use Siganushka\RegionBundle\Event\RegionFilterEvent;

class RegionFilterEventTest extends TestCase
{
    public function testRegionFilterEvent()
    {
        $region = new Region();
        $region->setCode('001');
        $region->setName('foo1');

        $array = [$region];
        $arrayCollection = new ArrayCollection($array);

        $event1 = new RegionFilterEvent($array);
        $event2 = new RegionFilterEvent($arrayCollection);

        $this->assertSame($array, $event1->getRegions());
        $this->assertSame($array, $event2->getRegions());
    }

    public function testRegionFilterEventException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('Array of regions must be type of %s', RegionInterface::class));

        new RegionFilterEvent([new \stdClass()]);
    }
}
