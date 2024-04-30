<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Tests\Entity;

use Siganushka\RegionBundle\Entity\Region;

final class RegionTest extends AbstractRegionTest
{
    public function testRegion(): void
    {
        $region = new Region();

        static::assertNull($region->getCode());
        static::assertNull($region->getName());

        $region->setCode('1');
        $region->setName('abcabcabcabcabcabcabcabcabcabcabcabcabc');

        static::assertSame('1', $region->getCode());
        static::assertSame('abcabcabcabcabcabcabcabcabcabcabcabcabc', $region->getName());
    }

    public function testParentConflictException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The parent conflict has been detected.');

        $this->city->setParent($this->city);
    }

    public function testDescendantConflictException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The descendants conflict has been detected.');

        $this->province->setParent($this->city);
    }
}
