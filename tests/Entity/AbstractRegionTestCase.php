<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Tests\Entity;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;
use Siganushka\RegionBundle\Dto\RegionQueryDto;
use Siganushka\RegionBundle\Entity\AbstractRegion;
use Siganushka\RegionBundle\Repository\RegionRepository;
use Siganushka\RegionBundle\Tests\Fixtures\FooRegion;

abstract class AbstractRegionTestCase extends TestCase
{
    protected AbstractRegion $province;
    protected AbstractRegion $city;
    protected AbstractRegion $district;

    protected RegionRepository $regionRepository;

    protected function setUp(): void
    {
        $district = new FooRegion('111000', 'baz');

        $city = new FooRegion('110000', 'bar');
        $city->addChild($district);

        $province = new FooRegion('100000', 'foo');
        $province->addChild($city);

        $regionRepository = $this->createMock(RegionRepository::class);

        $regionRepository->method('createQueryBuilderByDto')
            ->willReturnCallback(function ($_, RegionQueryDto $dto): QueryBuilder {
                $query = $this->createMock(Query::class);

                $query->method('getResult')
                    ->willReturn(match (true) {
                        null === $dto->parent => [$this->province],
                        '100000' === $dto->parent => [$this->city],
                        '110000' === $dto->parent => [$this->district],
                        default => [],
                    })
                ;

                $queryBuilder = $this->createMock(QueryBuilder::class);

                $queryBuilder->method('getQuery')
                    ->willReturn($query)
                ;

                return $queryBuilder;
            })
        ;

        $regionRepository->method('findByParent')
            ->willReturnCallback(static fn (mixed $parent): array => match (true) {
                null === $parent => [$province],
                '100000' === $parent => [$city],
                '110000' === $parent => [$district],
                default => [],
            })
        ;

        $regionRepository->method('find')
            ->willReturnCallback(static fn (mixed $code): ?AbstractRegion => match (true) {
                '100000' === $code => $province,
                '110000' === $code => $city,
                '111000' === $code => $district,
                default => null,
            })
        ;

        $this->province = $province;
        $this->city = $city;
        $this->district = $district;
        $this->regionRepository = $regionRepository;
    }
}
