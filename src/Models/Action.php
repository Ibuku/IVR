<?php
/**
 * Created by PhpStorm.
 * User: stikks
 * Date: 10/18/16
 * Time: 12:38 PM
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Action extends Model
{

    protected $table = 'actions';

    protected $fillable = [
        "campaign_id",
        "number",
        "body",
        "value",
        "script",
        "repeat_param",
        "confirm",
        "request",
        "parameter"
    ];
}