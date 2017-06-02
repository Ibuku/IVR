<?php

/**
 * Created by PhpStorm.
 * User: stikks
 * Date: 9/28/16
 * Time: 7:58 AM
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';

    protected $fillable = [
        'username',
        "password",
        "is_admin",
        "is_active"
    ];

    public function setPassword($password) {
        $this->update([
            'password' => openssl_digest($password, 'sha512')
        ]);
    }
}