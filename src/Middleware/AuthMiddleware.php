<?php
/**
 * Created by PhpStorm.
 * User: stikks
 * Date: 9/28/16
 * Time: 9:23 AM
 */

namespace App\Middleware;


class AuthMiddleware extends Middleware
{

    public function __invoke($request, $response, $next)
    {

        if (!$this->container->auth->check()) {

//            $this->container->flash->addMessage('error', 'You need to sign in to access this page');

            return $response->withRedirect($this->container->router->pathFor('login'));
        }

        return $next($request, $response);
    }
}