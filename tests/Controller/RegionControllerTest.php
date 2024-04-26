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

final class RegionControllerTest extends AbstractRegionTest
{
    protected ?RegionController $controller = null;

    protected function setUp(): void
    {
        parent::setUp();

        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $this->controller = new RegionController($serializer, $this->regionRepository);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->controller = null;
    }

    public function testGetCollection(): void
    {
        $response = $this->controller->getCollection(new Request());
        static::assertSame('[{"code":"100000","name":"foo","root":true,"leaf":false,"depth":0}]', $response->getContent());

        $response = $this->controller->getCollection(new Request(['parent' => '100000']));
        static::assertSame('[{"code":"110000","name":"bar","root":false,"leaf":false,"depth":1}]', $response->getContent());

        $response = $this->controller->getCollection(new Request(['parent' => '110000']));
        static::assertSame('[{"code":"111000","name":"baz","root":false,"leaf":true,"depth":2}]', $response->getContent());

        $response = $this->controller->getCollection(new Request(['parent' => 'invalid']));
        static::assertSame('[]', $response->getContent());

        $response = $this->controller->getCollection(new Request(['parent' => '']));
        static::assertSame('[]', $response->getContent());
    }

    public function testGetItem(): void
    {
        $response = $this->controller->getItem('100000');
        static::assertSame('{"code":"100000","name":"foo","root":true,"leaf":false,"depth":0}', $response->getContent());

        $response = $this->controller->getItem('110000');
        static::assertSame('{"code":"110000","name":"bar","root":false,"leaf":false,"depth":1}', $response->getContent());

        $response = $this->controller->getItem('111000');
        static::assertSame('{"code":"111000","name":"baz","root":false,"leaf":true,"depth":2}', $response->getContent());
    }

    public function testGetItemNotFoundHttpException(): void
    {
        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Resource #invalid_code not found.');

        $this->controller->getItem('invalid_code');
    }
}
