<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Tests\Entity;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;
use Siganushka\RegionBundle\Entity\Region;
use Siganushka\RegionBundle\Entity\RegionInterface;

abstract class AbstractRegionTest extends TestCase
{
    protected ?ManagerRegistry $managerRegistry = null;
    protected ?RegionInterface $province = null;
    protected ?RegionInterface $city = null;
    protected ?RegionInterface $district = null;

    protected function setUp(): void
    {
        $district = new Region();
        $district->setCode('3');
        $district->setName('baz');

        $city = new Region();
        $city->setCode('2');
        $city->setName('bar');
        $city->addChild($district);

        $province = new Region();
        $province->setCode('1');
        $province->setName('foo');
        $province->addChild($city);

        $objectRepository = $this->createMock(ObjectRepository::class);

        $objectRepository->expects(static::any())
            ->method('findBy')
            ->willReturn([$province])
        ;

        $objectRepository->expects(static::any())
            ->method('find')
            ->willReturnCallback(function ($value) use ($province) {
                return ('100000' === $value) ? $province : null;
            })
        ;

        $managerRegistry = $this->createMock(ManagerRegistry::class);

        $managerRegistry->expects(static::any())
            ->method('getRepository')
            ->willReturn($objectRepository)
        ;

        $this->managerRegistry = $managerRegistry;
        $this->province = $province;
        $this->city = $city;
        $this->district = $district;
    }

    protected function tearDown(): void
    {
        $this->managerRegistry = null;
        $this->province = null;
        $this->city = null;
        $this->district = null;
    }
}
