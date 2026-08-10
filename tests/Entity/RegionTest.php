<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Tests\Entity;

class RegionTest extends AbstractRegionTestCase
{
    public function testAll(): void
    {
        static::assertSame('100000', $this->province->getCode());
        static::assertSame('foo', $this->province->getName());

        static::assertSame('110000', $this->city->getCode());
        static::assertSame('bar', $this->city->getName());

        static::assertSame('111000', $this->district->getCode());
        static::assertSame('baz', $this->district->getName());

        static::assertSame([$this->province], $this->regionRepository->findByParent(null));
        static::assertSame([$this->city], $this->regionRepository->findByParent('100000'));
        static::assertSame([$this->district], $this->regionRepository->findByParent('110000'));
        static::assertSame([], $this->regionRepository->findByParent('123'));

        static::assertSame($this->province, $this->regionRepository->find('100000'));
        static::assertSame($this->city, $this->regionRepository->find('110000'));
        static::assertSame($this->district, $this->regionRepository->find('111000'));
        static::assertNull($this->regionRepository->find('123'));
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
