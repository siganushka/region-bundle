<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Tests\Controller;

use Siganushka\RegionBundle\Controller\RegionController;
use Siganushka\RegionBundle\Tests\Entity\AbstractRegionTest;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\AttributeMetadata;
use Symfony\Component\Serializer\Mapping\ClassMetadata;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

final class RegionControllerTest extends AbstractRegionTest
{
    protected RegionController $controller;

    protected function setUp(): void
    {
        parent::setUp();

        $attributesMetadata = [];
        foreach (['code', 'name', 'root', 'leaf', 'depth'] as $attribute) {
            $attributesMetadata[$attribute] = new AttributeMetadata($attribute);
            $attributesMetadata[$attribute]->addGroup('item');
            $attributesMetadata[$attribute]->addGroup('collection');
        }

        $metadata = $this->createMock(ClassMetadata::class);
        $metadata->expects(static::any())
            ->method('getAttributesMetadata')
            ->willReturn($attributesMetadata)
        ;

        $factory = $this->createMock(ClassMetadataFactoryInterface::class);
        $factory->expects(static::any())
            ->method('getMetadataFor')
            ->willReturn($metadata)
        ;

        $container = new Container();
        $container->set('serializer', new Serializer([new ObjectNormalizer($factory)], [new JsonEncoder()]));

        $this->controller = new RegionController($this->regionRepository);
        $this->controller->setContainer($container);
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
        $this->expectExceptionMessage('Not Found');

        $this->controller->getItem('invalid_code');
    }
}
