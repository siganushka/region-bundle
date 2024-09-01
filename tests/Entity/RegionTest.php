<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Tests\Entity;

use Siganushka\RegionBundle\Entity\Region;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class RegionTest extends AbstractRegionTest
{
    public function testRegion(): void
    {
        $region = new Region('100000', 'foo');

        static::assertSame('100000', $region->getCode());
        static::assertSame('foo', $region->getName());
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
