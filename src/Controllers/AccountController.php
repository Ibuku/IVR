<?php
/**
 * Created by PhpStorm.
 * User: stikks-workstation
 * Date: 6/1/17
 * Time: 5:05 PM
 */

namespace App\Controllers;
use App\Controllers\BaseController;
use App\Models\User;


class AccountController extends BaseController
{
    public function getPage($request, $response){

        $user = $this->auth->user();

        $users = User::all();

        if (!$user->is_admin) {
            return $response->withRedirect($this->router->pathFor('index'));
        }

        return $this->view->render($response, 'templates/accounts.twig', [
            'user' => $user,
            'accounts' => $users,
            'username' => $user->username
        ]);
    }

    public function createAccount($request, $response){

        $user = $this->auth->user();

        return $this->view->render($response, 'templates/forms/account.twig', [
            'user' => $user
        ]);
    }

    public function postData($request, $response){

        $match = ['username' => $request->getParam('name')];

        $account = User::where($match)->first();

        if ($account)
        {
            return $this->view->render($response, 'templates/forms/account.twig', [
                'user' => $this->auth->user(),
                'error' => "An account with this name already exists"
            ]);
        }

        $res = 0;
        if (!file_exists(realpath(__DIR__ . '/../..'). '/files/'. $request->getParam('name'))) {
            $res = mkdir(realpath(__DIR__ . '/../..'). '/files/'. $request->getParam('name'), 0777, true);
            chmod(realpath(__DIR__ . '/../..'). '/files/'. $request->getParam('name'), 0777);
        }

        if (!$res) {
            return $this->view->render($response, 'templates/forms/account.twig', [
                'user' => $this->auth->user(),
                'error' => "Account was not created, Couldn't create files folder"
            ]);
        }

        $sound_folder = 0;
        if (!file_exists('/var/lib/asterisk/sounds/files/'. $request->getParam('name'))) {
            $sound_folder = mkdir('/var/lib/asterisk/sounds/files/'. $request->getParam('name'), 0777, true);
            chmod('/var/lib/asterisk/sounds/files/'. $request->getParam('name'), 0777);
        }

//        $sound_folder = static::create_remotely($this->settings['REMOTE']['URL'], $this->settings['REMOTE']['USERNAME'],
//            $this->settings['REMOTE']['PASSWORD'], '/var/lib/asterisk/sounds/files/'. $request->getParam('name'));
//
        if (!$sound_folder) {
            return $this->view->render($response, 'templates/forms/account.twig', [
                'user' => $this->auth->user(),
                'error' => "Account was not created, Couldn't create sounds folder"
            ]);
        }

        $inactive_folder = 0;
        if (!file_exists('/var/lib/asterisk/sounds/files/inactive/'. $request->getParam('name'))) {
            $inactive_folder = mkdir('/var/lib/asterisk/sounds/files/inactive/'. $request->getParam('name'), 0777, true);
            chmod('/var/lib/asterisk/sounds/files/inactive/'. $request->getParam('name'), 0777);
        }
//        $inactive_folder = static::create_remotely($this->settings['REMOTE']['URL'], $this->settings['REMOTE']['USERNAME'],
//            $this->settings['REMOTE']['PASSWORD'], '/var/lib/asterisk/sounds/files/inactive/'. $request->getParam('name'));
//
        if (!$inactive_folder) {
            return $this->view->render($response, 'templates/forms/account.twig', [
                'user' => $this->auth->user(),
                'error' => "Account was not created, Couldn't create inactive sounds folder"
            ]);
        }

        $account = User::create([
            'username' => $request->getParam('name'),
            'password' => openssl_digest($request->getParam('password'), 'sha512'),
            'is_active' => true,
            'is_admin' => false
        ]);

        if ($account) {
            return $response->withRedirect($this->router->pathFor('accounts'));
        }

        return $this->view->render($response, 'templates/forms/account.twig', [
            'user' => $this->auth->user(),
            'error' => "Account was not created"
        ]);
    }

    public function Deactivate($request, $response, $args) {

        if (!isset($args['user_id'])) {
            return $response->withStatus(404);
        };

        $match = ['id' => $args['user_id']];

        $account = User::where($match)->first();

        if (!$account) {
            return $response->withStatus(404);
        };

        $account->update([
            'is_active' => false
        ]);

        try {
            rename('/var/lib/asterisk/sounds/files/'. $account->username, '/var/lib/asterisk/sounds/inactive/'. $account->username);
//            $transfer = static::rename_remotely($this->settings['REMOTE']['URL'], $this->settings['REMOTE']['USERNAME'],
//                $this->settings['REMOTE']['PASSWORD'], '/var/lib/asterisk/sounds/files/'. $account->username,
//                '/var/lib/asterisk/sounds/inactive/'. $account->username);
//            if (!$transfer) {
//                return $response->withStatus(400);
//            }
        }
        catch (\Exception $e) {
            return $response->withStatus(400);
        }

        return $response->withStatus(200);
    }

    public function Activate($request, $response, $args) {

        if (!isset($args['user_id'])) {
            return $response->withStatus(404);
        };

        $match = ['id' => $args['user_id']];

        $account = User::where($match)->first();

        if (!$account) {
            return $response->withStatus(404);
        };

        $account->update([
            'is_active' => true
        ]);

        try {
//            $transfer = static::rename_remotely($this->settings['REMOTE']['URL'], $this->settings['REMOTE']['USERNAME'],
//                $this->settings['REMOTE']['PASSWORD'], '/var/lib/asterisk/sounds/inactive/'. $account->username,
//                '/var/lib/asterisk/sounds/files/'. $account->username);
//            if (!$transfer) {
//                return $response->withStatus(400);
//            }
            rename('/var/lib/asterisk/sounds/inactive/'. $account->username, '/var/lib/asterisk/sounds/files/'. $account->username);
        }
        catch (\Exception $e) {
            return $response->withStatus(400);
        }

        return $response->withStatus(200);
    }

}