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
        "default_settings",
        "incorrect_path",
        "no_selection_path",
        "repeat_path",
        "success_path",
        "goodbye_path",
        "subscription_path",
        "subscription_confirmation_path",
        "insufficient_balance_path",
        "already_subscribed_path",
        "subscription_failure_path",
        "continue_path",
        "wrong_selection_path"
    ];
}