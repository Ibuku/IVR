<?php
/**
 * Created by PhpStorm.
 * User: stikks
 * Date: 9/28/16
 * Time: 9:28 AM
 */

namespace App\Middleware;


class GuestMiddleware extends Middleware
{
    public function __invoke($request, $response, $next)
    {
        // TODO: Implement __invoke() method.
        if($this->container->auth->check()) {

            return $response->withRedirect($this->container->router->pathFor('index'));
        }

        return $next($request, $response);
    }
}