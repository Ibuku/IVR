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

    public function compare($new_password, $repeat_password) {
        return $new_password === $repeat_password;
    }

    public function check_password($username, $password) {

        $user = User::where('username', $username)->first();

        if (!$user) {
            return false;
        }

        if ($user->password == openssl_digest($password, 'sha512')) {
            return true;
        }
        return false;
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

    public function create_account($username, $password, $is_admin=False) {
        $user = User::where('username', $username)->first();

        if (!$user) {
            return $user;
        }

        try {
            $user = User::create([
                'username' => $username,
                'password' => openssl_digest($password, 'sha512'),
                'is_admin' => $is_admin
            ]);

            return $user;
        } catch (\Exception $e) {
            throw $e;
        }
    }

}