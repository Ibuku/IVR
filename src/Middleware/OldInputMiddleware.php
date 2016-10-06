<?php
/**
 * Created by PhpStorm.
 * User: stikks
 * Date: 9/28/16
 * Time: 8:09 AM
 */

namespace App\Middleware;


class OldInputMiddleware extends Middleware
{
    public function __invoke($request, $response, $next) {

        $this->container->view->getEnvironment()->addGlobal('old', $_SESSION['old']);
        $_SESSION['old'] = $request->getParams();

        return $next($request, $response);
    }
}