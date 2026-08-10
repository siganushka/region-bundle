<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Tests\Controller;

use Siganushka\RegionBundle\Controller\RegionController;
use Siganushka\RegionBundle\Dto\RegionQueryDto;
use Siganushka\RegionBundle\Tests\Entity\AbstractRegionTestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\YamlFileLoader;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class RegionControllerTest extends AbstractRegionTestCase
{
    protected RegionController $controller;

    protected function setUp(): void
    {
        parent::setUp();

        $loader = new YamlFileLoader(__DIR__.'/../../config/serialization/Region.yaml');
        $factory = new ClassMetadataFactory($loader);

        $container = new Container();
        $container->set('serializer', new Serializer([new ObjectNormalizer($factory)], [new JsonEncoder()]));

        $this->controller = new RegionController($this->regionRepository);
        $this->controller->setContainer($container);
    }

    public function testGetCollection(): void
    {
        $response = $this->controller->getCollection(new RegionQueryDto(null, null));
        static::assertSame('[{"code":"100000","name":"foo","depth":0,"root":true,"leaf":false}]', $response->getContent());

        $response = $this->controller->getCollection(new RegionQueryDto('100000', null));
        static::assertSame('[{"code":"110000","name":"bar","depth":1,"root":false,"leaf":false}]', $response->getContent());

        $response = $this->controller->getCollection(new RegionQueryDto('110000', null));
        static::assertSame('[{"code":"111000","name":"baz","depth":2,"root":false,"leaf":true}]', $response->getContent());

        $response = $this->controller->getCollection(new RegionQueryDto('111000', null));
        static::assertSame('[]', $response->getContent());

        $response = $this->controller->getCollection(new RegionQueryDto('123', null));
        static::assertSame('[]', $response->getContent());
    }

    public function testGetItem(): void
    {
        $response = $this->controller->getItem('100000');
        static::assertSame('{"code":"100000","name":"foo","depth":0,"root":true,"leaf":false}', $response->getContent());

        $response = $this->controller->getItem('110000');
        static::assertSame('{"code":"110000","name":"bar","depth":1,"root":false,"leaf":false}', $response->getContent());

        $response = $this->controller->getItem('111000');
        static::assertSame('{"code":"111000","name":"baz","depth":2,"root":false,"leaf":true}', $response->getContent());
    }

    public function testGetItemNotFoundHttpException(): void
    {
        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Not Found');

        $this->controller->getItem('invalid_code');
    }
}
