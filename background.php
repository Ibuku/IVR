<?php
/**
 * Created by PhpStorm.
 * User: stikks
 * Date: 10/19/16
 * Time: 10:54 AM
 */
date_default_timezone_set('Africa/Lagos');
require __DIR__ . '/bootstrap/app.php';
use App\Models\Campaign;
use App\Models\Files;

$now = date('Y-m-d');

$campaigns = Campaign::all();

foreach ($campaigns as $value) {
    $_date = date($value->end_date);
    if ($_date < $now) {
        $value->deactivate();
        $file = Files::where('file_path', $value->file_path)->first();
        unlink("/var/lib/asterisk/sounds/files/" . $value->username . '/' . $file->name);
    }
}