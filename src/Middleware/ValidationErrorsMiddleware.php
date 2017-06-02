<?php
/**
 * Created by PhpStorm.
 * User: stikks
 * Date: 9/28/16
 * Time: 8:10 AM
 */

namespace App\Middleware;


class ValidationErrorsMiddleware extends Middleware
{

    public function __invoke($request, $response, $next) {

        if (isset($_SESSION['errors'])) {
            $this->container->view->getEnvironment()->addGlobal('errors', $_SESSION['errors']);
        }

        unset($_SESSION['errors']);

        $response = $next($request, $response);

        return $response;
    }
}