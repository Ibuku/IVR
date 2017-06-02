<?php
/**
 * Created by PhpStorm.
 * User: stikks
 * Date: 10/12/16
 * Time: 11:08 AM
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Files extends Model
{

    protected $table = 'files';

    protected $fillable = [
        "username",
        "file_path",
        "duration",
        "name",
        "file_type",
        "size",
        "description",
        "tag"
    ];
    
}