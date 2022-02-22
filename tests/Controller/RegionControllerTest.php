<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Tests\Controller;

use Siganushka\RegionBundle\Controller\RegionController;
use Siganushka\RegionBundle\Serializer\Normalizer\RegionNormalizer;
use Siganushka\RegionBundle\Tests\Entity\AbstractRegionTest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Serializer;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class RegionControllerTest extends AbstractRegionTest
{
    protected $controller;

    protected function setUp(): void
    {
        parent::setUp();

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $normalizer = new Serializer([new RegionNormalizer()]);

        $this->controller = new RegionController($eventDispatcher, $this->managerRegistry, $normalizer);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->controller = null;
    }

    public function testInvoke(): void
    {
        $request = new Request();
        $response = $this->controller->__invoke($request);

        static::assertSame('[{"code":"100000","name":"foo"}]', $response->getContent());

        $request = new Request(['parent' => '100000']);
        $response = $this->controller->__invoke($request);

        static::assertSame('[{"code":"200000","name":"bar"}]', $response->getContent());
    }

    public function testGetRegions(): void
    {
        $method = new \ReflectionMethod($this->controller, 'getRegions');
        $method->setAccessible(true);

        $request = new Request();
        $regions = $method->invokeArgs($this->controller, [$request]);

        static::assertSame([$this->province], $regions);

        $request = new Request(['parent' => '100000']);
        $regions = $method->invokeArgs($this->controller, [$request]);

        static::assertSame($this->province->getChildren(), $regions);
    }

    public function testGetRegionsException(): void
    {
        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('The parent "123" could not be found.');

        $method = new \ReflectionMethod($this->controller, 'getRegions');
        $method->setAccessible(true);

        $request = new Request(['parent' => '123']);
        $method->invokeArgs($this->controller, [$request]);
    }
}
