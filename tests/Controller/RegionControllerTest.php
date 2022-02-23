<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Tests\Controller;

use Siganushka\RegionBundle\Controller\RegionController;
use Siganushka\RegionBundle\Tests\Entity\AbstractRegionTest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class RegionControllerTest extends AbstractRegionTest
{
    protected ?RegionController $controller = null;

    protected function setUp(): void
    {
        parent::setUp();

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $this->controller = new RegionController($eventDispatcher, $this->managerRegistry, $serializer);
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

        $request = new Request(['parent' => '100000', 'attributes' => 'root,leaf,depth,foo']);
        $response = $this->controller->__invoke($request);

        static::assertSame('[{"code":"200000","name":"bar","root":false,"leaf":false,"depth":1}]', $response->getContent());
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
