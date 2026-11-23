<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Tests\Command;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Siganushka\RegionBundle\Command\RegionUpdateCommand;
use Siganushka\RegionBundle\Repository\RegionRepository;
use Siganushka\RegionBundle\Tests\Fixtures\TestRegion;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class RegionUpdateCommandTest extends TestCase
{
    public function testExecute(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->expects(static::once())
            ->method('request')
            ->with('GET', 'https://raw.githubusercontent.com/modood/Administrative-divisions-of-China/master/dist/pca-code.json')
            ->willReturn($this->createResponse([
                ['code' => '100000', 'name' => 'foo', 'children' => [
                    ['code' => '110000', 'name' => 'bar'],
                ]],
            ]))
        ;

        $regionRepository = $this->createMock(RegionRepository::class);
        $regionRepository->method('createNew')
            ->willReturnCallback(static fn (string $code, string $name): TestRegion => new TestRegion($code, $name))
        ;
        $regionRepository->method('findAll')->willReturn([]);

        /** @var list<TestRegion> $persisted */
        $persisted = [];
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects(static::exactly(2))
            ->method('persist')
            ->willReturnCallback(static function (object $entity) use (&$persisted): void {
                static::assertInstanceOf(TestRegion::class, $entity);
                $persisted[] = $entity;
            })
        ;
        $entityManager->expects(static::once())->method('flush');

        $tester = new CommandTester(new RegionUpdateCommand($httpClient, $entityManager, $regionRepository));
        $status = $tester->execute(['--level' => '3']);

        static::assertSame(Command::SUCCESS, $status);
        static::assertSame(['100000', '110000'], array_map(static fn (TestRegion $region) => $region->getCode(), $persisted));
        static::assertSame($persisted[0], $persisted[1]->getParent());

        static::assertStringContainsString('[100000] foo 导入成功！', $tester->getDisplay());
        static::assertStringContainsString('[110000] bar 导入成功！', $tester->getDisplay());
    }

    public function testExecuteSkipsExistingRegions(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->method('request')
            ->willReturn($this->createResponse([
                ['code' => '100000', 'name' => 'foo', 'children' => [
                    ['code' => '110000', 'name' => 'bar'],
                ]],
            ]))
        ;

        $existing = new TestRegion('100000', 'foo');

        $regionRepository = $this->createMock(RegionRepository::class);
        $regionRepository->method('createNew')
            ->willReturnCallback(static fn (string $code, string $name): TestRegion => new TestRegion($code, $name))
        ;
        $regionRepository->method('findAll')->willReturn([$existing]);

        /** @var list<TestRegion> $persisted */
        $persisted = [];
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects(static::once())
            ->method('persist')
            ->willReturnCallback(static function (object $entity) use (&$persisted): void {
                static::assertInstanceOf(TestRegion::class, $entity);
                $persisted[] = $entity;
            })
        ;

        $tester = new CommandTester(new RegionUpdateCommand($httpClient, $entityManager, $regionRepository));
        $status = $tester->execute(['--level' => '2']);

        static::assertSame(Command::SUCCESS, $status);
        static::assertCount(1, $persisted);
        static::assertSame('110000', $persisted[0]->getCode());
        static::assertSame($existing, $persisted[0]->getParent());

        static::assertStringContainsString('[100000] foo 已存在！', $tester->getDisplay());
        static::assertStringContainsString('[110000] bar 导入成功！', $tester->getDisplay());
    }

    public function testExecuteWithInvalidLevel(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->expects(static::never())->method('request');

        $command = new RegionUpdateCommand(
            $httpClient,
            $this->createMock(EntityManagerInterface::class),
            $this->createMock(RegionRepository::class)
        );

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The option "level" with value "5" is invalid (2, 3, 4).');

        (new CommandTester($command))->execute(['--level' => '5']);
    }

    private function createResponse(array $data): ResponseInterface
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->method('toArray')->willReturn($data);

        return $response;
    }
}
