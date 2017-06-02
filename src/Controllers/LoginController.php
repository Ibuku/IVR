<?php

/**
 * Created by PhpStorm.
 * User: stikks
 * Date: 9/28/16
 * Time: 8:13 AM
 */
namespace App\Controllers;
use App\Models\User;

class LoginController extends BaseController
{

    public function getPage($request, $response) {
        return $this->view->render($response, 'templates/login.twig');
    }

    public function postData($request, $response) {

        $auth = $this->container->auth->attempt(
            $request->getParam('username'),
            $request->getParam('password')
        );

        if(!$auth) {
            return $this->view->render($response, 'templates/login.twig', [
                'error' => 'Invalid username/password combination'
            ]);
        }

        return $response->withRedirect($this->router->pathFor('index'));
    }

    public function forgotPassword($request, $response) {
        return $this->view->render($response, 'templates/forgot.twig');
    }

    public function postForgot($request, $response) {

        $cmp = $this->container->auth->compare(
            $request->getParam('new_password'),
            $request->getParam('repeat_password')
        );

        if(!$cmp) {
            return $this->view->render($response, 'templates/forgot.twig', [
                'error' => 'Passwords do not match'
            ]);
        }

        $auth = $this->container->auth->check_password(
            $request->getParam('username'),
            $request->getParam('current_password')
        );

        if(!$auth) {
            return $this->view->render($response, 'templates/forgot.twig', [
                'error' => 'Invalid username/password combination'
            ]);
        }

        $user = User::where('username', $request->getParam('username'))->first();

        $user->setPassword($request->getParam('new_password'));

        return $response->withRedirect($this->router->pathFor('login'));
    }
}