<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Tests\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use Siganushka\RegionBundle\Dto\RegionQueryDto;
use Siganushka\RegionBundle\Repository\RegionRepository;
use Siganushka\RegionBundle\Tests\Fixtures\TestRegion;

class RegionRepositoryTest extends TestCase
{
    public function testCreateQueryBuilderByDto(): void
    {
        $repository = $this->createRepository(TestRegion::class);

        static::assertSame(
            'SELECT r FROM Siganushka\RegionBundle\Tests\Fixtures\TestRegion r WHERE r.parent IS NULL ORDER BY r.parent ASC, r.code ASC',
            $repository->createQueryBuilderByDto('r', new RegionQueryDto(null, null))->getDQL()
        );

        static::assertSame(
            'SELECT r FROM Siganushka\RegionBundle\Tests\Fixtures\TestRegion r WHERE r.parent = :parent ORDER BY r.parent ASC, r.code ASC',
            $repository->createQueryBuilderByDto('r', new RegionQueryDto('100000', null))->getDQL()
        );

        static::assertSame(
            'SELECT r FROM Siganushka\RegionBundle\Tests\Fixtures\TestRegion r WHERE r.parent = :parent AND r.name LIKE :name ORDER BY r.parent ASC, r.code ASC',
            $repository->createQueryBuilderByDto('r', new RegionQueryDto('100000', 'foo'))->getDQL()
        );

        static::assertSame(
            'SELECT r FROM Siganushka\RegionBundle\Tests\Fixtures\TestRegion r WHERE r.name LIKE :name ORDER BY r.parent ASC, r.code ASC',
            $repository->createQueryBuilderByDto('r', new RegionQueryDto(null, 'foo'))->getDQL()
        );
    }

    /**
     * @param class-string<TestRegion> $entityClass
     */
    private function createRepository(string $entityClass): RegionRepository
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);

        $entityManager->method('getClassMetadata')
            ->willReturn(new ClassMetadata($entityClass))
        ;

        $entityManager->method('createQueryBuilder')
            // Using willReturnCallback to create new instance
            ->willReturnCallback(static fn () => new QueryBuilder($entityManager))
        ;

        $managerRegistry = $this->createMock(ManagerRegistry::class);

        $managerRegistry->method('getManagerForClass')
            ->willReturn($entityManager)
        ;

        return new RegionRepository($managerRegistry, $entityClass);
    }
}
