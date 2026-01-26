<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use Siganushka\RegionBundle\Entity\Region;

class RegionTest extends TestCase
{
    use RegionTestTrait;

    public function testAll(): void
    {
        static::assertSame('100000', $this->province->getCode());
        static::assertSame('foo', $this->province->getName());
        static::assertSame('foo', $this->province->getFullname());
        static::assertSame(1, $this->province->getLevel());

        static::assertSame('110000', $this->city->getCode());
        static::assertSame('bar', $this->city->getName());
        static::assertSame('foo/bar', $this->city->getFullname());
        static::assertSame(2, $this->city->getLevel());

        static::assertSame('111000', $this->district->getCode());
        static::assertSame('baz', $this->district->getName());
        static::assertSame('foo/bar/baz', $this->district->getFullname());
        static::assertSame(3, $this->district->getLevel());
    }

    public function testLazyGetter(): void
    {
        $region = new Region('foo', 'bar');

        $fullnameRef = new \ReflectionProperty($region, 'fullname');
        $levelRef = new \ReflectionProperty($region, 'level');
        static::assertNull($fullnameRef->getValue($region));
        static::assertNull($levelRef->getValue($region));

        static::assertSame('bar', $region->getFullname());
        static::assertSame(1, $region->getLevel());

        $region->setParent(new Region('hello', 'hello'));
        static::assertSame('hello/bar', $region->getFullname());
        static::assertSame(2, $region->getLevel());

        $region->addChild($child = new Region('world', 'world'));
        static::assertSame('hello/bar/world', $child->getFullname());
        static::assertSame(3, $child->getLevel());

        $region->removeChild($child);
        static::assertSame('hello/bar', $region->getFullname());
        static::assertSame(2, $region->getLevel());
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
