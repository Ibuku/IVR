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
use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;

$log = new Logger('recordsLog');
$handler = new RotatingFileHandler('/opt/IVR/logs/status.log', 0, Logger::INFO);
$log->pushHandler($handler);

$now = date('Y-m-d');

$campaigns = Campaign::all();

foreach ($campaigns as $value) {
    $_date = date($value->end_date);
    if ($_date < $now) {
        $file_split = explode('/', $value->play_path);
        $file_name = end($file_split);
        try {
            rename($value->play_path, '/var/lib/asterisk/sounds/files/inactive/'. $value->username. '/'. $file_name);
            $value->deactivate();
            $record = '[DATETIME:' . time() . '][STATUS: Successful][Campaign:'.$value->name .' ][FILE_PATH:' . $value->play_path . ']';
            $log->info($record);
        } catch (Exception $e) {
            $record = '[DATETIME:' . time() . '][STATUS: Failed][Campaign:'.$value->name .' ][FILE_PATH:' . $value->play_path . ']';
            $log->info($record);
        }

    }
}