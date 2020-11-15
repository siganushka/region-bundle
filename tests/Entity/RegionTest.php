<?php

namespace Siganushka\RegionBundle\Tests\Entity;

use Siganushka\GenericBundle\Exception\TreeDescendantConflictException;
use Siganushka\RegionBundle\Entity\Region;
use Siganushka\RegionBundle\Entity\RegionInterface;

class RegionTest extends AbstractRegionTest
{
    public function testRegion()
    {
        $region = new Region();

        $this->assertNull($region->getCode());
        $this->assertNull($region->getName());
        $this->assertInstanceOf(RegionInterface::class, $region);

        $region->setCode('1');
        $region->setName('abcabcabcabcabcabcabcabcabcabcabcabcabc');

        $this->assertSame('100000', $region->getCode());
        $this->assertSame('abcabcabcabcabcabcabcabcabcabcab', $region->getName());
    }

    public function testTreeDescendantConflictException()
    {
        $this->expectException(TreeDescendantConflictException::class);

        $this->province->setParent($this->city);
    }
}
