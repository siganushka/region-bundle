<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Tests\Routing;

use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Routing\AttributeRouteControllerLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Routing\Loader\AttributeDirectoryLoader;
use Symfony\Component\Routing\Loader\PhpFileLoader;

class LoaderTest extends TestCase
{
    public function testRotues(): void
    {
        $locator = new FileLocator(__DIR__.'/../../config/');

        $resolver = new LoaderResolver([
            new PhpFileLoader($locator),
            new AttributeDirectoryLoader($locator, new AttributeRouteControllerLoader()),
        ]);

        $loader = new DelegatingLoader($resolver);
        $routes = $loader->load('routes.php');

        static::assertSame('/regions', $routes->get('siganushka_region_region_getcollection')->getPath());
        static::assertSame('/regions/{code}', $routes->get('siganushka_region_region_getitem')->getPath());
    }
}
