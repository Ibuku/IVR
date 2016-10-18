<?php

/**
 * Created by PhpStorm.
 * User: stikks
 * Date: 9/28/16
 * Time: 7:55 AM
 */
namespace App\Auth;
use App\Models\User;

class Auth
{

    public function check() {
        if (isset($_SESSION['user'])){
            $user = User::where('id', $_SESSION['user'])->first();
            if ($user) {
                return true;
            }
            unset($_SESSION['user']);
        };
        return false;
    }

    public function user() {
        $user = User::where('id', $_SESSION['user'])->first();
        return User::where('username', $user->username)->first();
    }

    public function attempt($username, $password) {

        $user = User::where('username', $username)->first();

        if (!$user) {
            return false;
        }

        $check = openssl_digest($password, 'sha512');

        if ($check == $user->password) {
            $_SESSION['user'] = $user->id;
            return true;
        }
        return false;
    }

    public function logout() {
        unset($_SESSION['user']);
    }

}