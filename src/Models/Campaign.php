<?php
/**
 * Created by PhpStorm.
 * User: stikks
 * Date: 10/17/16
 * Time: 12:11 PM
 */

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
  protected $table = 'campaigns';

    protected $fillable = [
        "username",
        "file_path",
        "start_date",
        "name",
        "end_date",
        "description"
    ];

//    public function updateData($name, $description, $start_date, $end_date) {
//
//        $this->update([
//            'name' => $name
//        ]);
//        echo $this;
//        exit();
//    }

}