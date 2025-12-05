<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Tests;

use PHPUnit\Framework\TestCase;
use Siganushka\RegionBundle\Controller\RegionController;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\PhpFileLoader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class RoutesTest extends TestCase
{
    protected RouteCollection $routes;

    protected function setUp(): void
    {
        $loader = new PhpFileLoader(new FileLocator(__DIR__.'/../config/'));
        $this->routes = $loader->load('routes.php');
    }

    public function testAll(): void
    {
        $routes = iterator_to_array(self::routesProvider());
        $routeNames = array_map(fn (array $route) => $route[0], $routes);

        static::assertSame($routeNames, array_keys($this->routes->all()));
    }

    /**
     * @dataProvider routesProvider
     */
    public function testRotues(string $routeName, string $path, array $methods, array $controller): void
    {
        /** @var Route */
        $route = $this->routes->get($routeName);

        static::assertSame($path, $route->getPath());
        static::assertSame($methods, $route->getMethods());
        static::assertSame($controller, $route->getDefault('_controller'));
        static::assertTrue($route->getDefault('_stateless'));
    }

    public static function routesProvider(): iterable
    {
        yield ['siganushka_region_getcollection', '/regions', ['GET'], [RegionController::class, 'getCollection']];
        yield ['siganushka_region_getitem', '/regions/{code}', ['GET'], [RegionController::class, 'getItem']];
    }
}
