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
        "wrong_path",
        "success_path",
        "failure_path",
        "goodbye_path",
        "continue_path",
        "no_selection_path",
        "selection_confirmation_path",
        "subscription_failure_path",
        "default_settings"
    ];
}