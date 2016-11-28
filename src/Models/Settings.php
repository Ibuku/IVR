<?php
/**
 * Created by PhpStorm.
 * User: stikks-workstation
 * Date: 11/23/16
 * Time: 2:23 PM
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $table = 'settings';

    protected $fillable = [
        "advert_limit",
        "incorrect_path",
        "repeat_path",
        "confirmation_path",
    ];
}