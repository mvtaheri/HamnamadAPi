<?php

namespace App\Helpers;


/**
 * Class Route
 * @package App\Helpers\Router
 */
class Route
{
    /**
     * @var
     */
    protected $app;

    /**
     * Route constructor.
     * @param $app
     */

    function __construct($app)
    {

        $this->app = $app;
    }

    /**
     * @param $url
     * @param $controller
     * @param array $args
     */
    public  function resource($url, $controller, $args = [])
    {
       $this->app->group($url, function () use ($controller, $args) {

            $this->get('', $controller . ':index')->add(self::middleware('index', $args));
            $this->get('/create', $controller . ':create')->add(self::middleware('create', $args));
            $this->get('/{id:[0-9]+}', $controller . ':show')->add(self::middleware('show', $args));
            $this->get('/{id:[0-9]+}/edit', $controller . ':edit')->add(self::middleware('edit', $args));
            $this->post('', $controller . ':store')->add(self::middleware('store', $args));
            $this->put('/{id:[0-9]+}', $controller . ':update')->add(self::middleware('update', $args));
            $this->patch('/{id:[0-9]+}', $controller . ':update')->add(self::middleware('index', $args));
            $this->delete('/{id:[0-9]+}', $controller . ':destroy')->add(self::middleware('destroy', $args));

        })->add(self::middleware('group', $args));
    }

    /**
     * @param string $middlewareType
     * @param $args
     * @return \Closure
     */
    public static function middleware($middlewareType = "group", $args)
    {
        $defaultMiddleware = function ($request, $response, $next) {
            return $next($request, $response);
        };

        return $middleware = $args['middleware'][$middlewareType] ?? $defaultMiddleware;
    }
}