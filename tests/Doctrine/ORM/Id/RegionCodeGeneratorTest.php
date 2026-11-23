<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Tests\Doctrine\ORM\Id;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Siganushka\RegionBundle\Doctrine\ORM\Id\RegionCodeGenerator;
use Siganushka\RegionBundle\Tests\Fixtures\TestRegion;

final class RegionCodeGeneratorTest extends TestCase
{
    public function testGenerateId(): void
    {
        $generator = new RegionCodeGenerator();
        $entityManager = $this->createMock(EntityManagerInterface::class);

        static::assertSame('100000', $generator->generateId($entityManager, new TestRegion('100000', 'foo')));
        static::assertNull($generator->generateId($entityManager, new \stdClass()));
        static::assertNull($generator->generateId($entityManager, null));
    }
}
