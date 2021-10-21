<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Tests\Entity;

use Siganushka\GenericBundle\Exception\TreeDescendantConflictException;
use Siganushka\RegionBundle\Entity\Region;
use Siganushka\RegionBundle\Entity\RegionInterface;

/**
 * @internal
 * @coversNothing
 */
final class RegionTest extends AbstractRegionTest
{
    public function testRegion(): void
    {
        $region = new Region();

        static::assertNull($region->getCode());
        static::assertNull($region->getName());
        static::assertInstanceOf(RegionInterface::class, $region);

        $region->setCode('1');
        $region->setName('abcabcabcabcabcabcabcabcabcabcabcabcabc');

        static::assertSame('100000', $region->getCode());
        static::assertSame('abcabcabcabcabcabcabcabcabcabcab', $region->getName());
    }

    public function testTreeDescendantConflictException(): void
    {
        $this->expectException(TreeDescendantConflictException::class);

        $this->province->setParent($this->city);
    }
}
