<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;

class RegionTest extends TestCase
{
    use RegionTestTrait;

    public function testAll(): void
    {
        static::assertSame('100000', $this->province->getCode());
        static::assertSame('foo', $this->province->getName());

        static::assertSame('110000', $this->city->getCode());
        static::assertSame('bar', $this->city->getName());

        static::assertSame('111000', $this->district->getCode());
        static::assertSame('baz', $this->district->getName());
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
