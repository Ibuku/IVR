<?php
/**
 * Created by PhpStorm.
 * User: stikks
 * Date: 9/28/16
 * Time: 8:14 AM
 */

namespace App\Controllers;
use App\Services\Converter;
use App\Models\Campaign;


class IndexController extends BaseController
{
    public function index($request, $response){

        $user = $this->auth->user();
        
        $campaigns = Campaign::all();
            
        return $this->view->render($response, 'templates/home.twig', [
            'user' => $user,
            'data' => $campaigns,
            'username' => 'all'
//            'username' => $user->username
        ]);
    }

    public function logOut($request, $response) {

        $this->auth->logout();

        return $response->withRedirect($this->router->pathFor('login'));
    }

}
