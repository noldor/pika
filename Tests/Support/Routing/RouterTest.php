<?php

declare(strict_types=1);

namespace Tests\Support\Routing;

use ArrayObject;
use RuntimeException;
use Support\Exceptions\UnknownRouteException;
use Support\Routing\Router;
use Tests\TestCase;

class RouterTest extends TestCase
{
    /**
     * @var \Support\Routing\Router
     */
    private $router;

    protected function setUp(): void
    {
        $this->router = new Router;
    }

    public function testThatRouterCanLoadRoutesFromFile(): void
    {
        $this->router->loadRoutes(__DIR__ . '/routes_stub.php');

        $this->assertSame(
            [
                'get' => ['get_route' => ArrayObject::class],
                'post' => ['post_route' => ArrayObject::class],
                'put' => ['put_route' => ArrayObject::class],
                'delete' => ['delete_route' => ArrayObject::class]
            ],
            $this->router->toArray()
        );
    }

    public function testLoadRoutesReturnSelf(): void
    {
        $this->assertSame($this->router, $this->router->loadRoutes(__DIR__ . '/routes_stub.php'));
    }

    public function testRouterCanAddGetRoute(): void
    {
        $this->router->get('new_get', ArrayObject::class);

        $this->assertSame(['new_get' => ArrayObject::class], $this->router->toArray()['get']);
    }

    public function testRouterGetThrowExceptionWhenNotClassNameHandlerGiven(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Route handler must be a valid class name!');

        $this->router->get('new_get', 'not class');
    }

    public function testRouterCanAddPostRoute(): void
    {
        $this->router->post('new_post', ArrayObject::class);

        $this->assertSame(['new_post' => ArrayObject::class], $this->router->toArray()['post']);
    }

    public function testRouterPostThrowExceptionWhenNotClassNameHandlerGiven(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Route handler must be a valid class name!');

        $this->router->post('new_post', 'not class');
    }

    public function testRouterCanAddPutRoute(): void
    {
        $this->router->put('new_put', ArrayObject::class);

        $this->assertSame(['new_put' => ArrayObject::class], $this->router->toArray()['put']);
    }

    public function testRouterPutThrowExceptionWhenNotClassNameHandlerGiven(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Route handler must be a valid class name!');

        $this->router->put('new_put', 'not class');
    }

    public function testRouterCanAddDeleteRoute(): void
    {
        $this->router->delete('new_delete', ArrayObject::class);

        $this->assertSame(['new_delete' => ArrayObject::class], $this->router->toArray()['delete']);
    }

    public function testRouterDeleteThrowExceptionWhenNotClassNameHandlerGiven(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Route handler must be a valid class name!');

        $this->router->delete('new_delete', 'not class');
    }

    public function testRouterHasReturnTrueWhenMethodIsUpperCasedAndRouteExist(): void
    {
        $this->router->get('first', ArrayObject::class);

        $this->assertTrue($this->router->has('GET', 'first'));
    }

    public function testRouterHasReturnFalseWhenUnknownMethodPassed(): void
    {
        $this->assertFalse($this->router->has('SOME', 'route'));
    }

    public function testRouterHasReturnFalseWhenKnownMethodPassedButRouteDoesNotExist(): void
    {
        $this->assertFalse($this->router->has('GET', 'some'));
    }

    public function testRouterHasReturnTrueWhenRouteHasTrailingSlashesAndRouteExist(): void
    {
        $this->router->get('first', ArrayObject::class);

        $this->assertTrue($this->router->has('GET', '/first/'));
    }

    public function testRouterGetHandlerThrowExceptionWhenRouteDoesNotExist(): void
    {
        $this->expectException(UnknownRouteException::class);
        $this->expectExceptionMessage('Unknown route <any> for method <get>!');

        $this->router->getHandler('GET', 'any');
    }

    public function testRouterGetHandlerReturnHandler(): void
    {
        $this->router->get('some', ArrayObject::class);

        $this->assertSame(ArrayObject::class, $this->router->getHandler('GET', 'some'));
    }
}
