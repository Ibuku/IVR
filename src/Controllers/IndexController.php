<?php
/**
 * Created by PhpStorm.
 * User: stikks
 * Date: 9/28/16
 * Time: 8:14 AM
 */

namespace App\Controllers;
use App\Models\User;
use App\Services\Converter;
use App\Models\Campaign;


class IndexController extends BaseController
{
    public function index($request, $response){

        $user = $this->auth->user();

        $active_campaigns = Campaign::where('is_active', true)->get();
            
        return $this->view->render($response, 'templates/home.twig', [
            'user' => $user,
            'data' => Campaign::all(),
            'username' => 'all',
            'active' => $active_campaigns,
            'users' => User::all()
        ]);
    }

    public function logOut($request, $response) {

        $this->auth->logout();

        return $response->withRedirect($this->router->pathFor('login'));
    }

}
