#!/usr/bin/php -q
<?php
/**
 * Created by PhpStorm.
 * User: stikks-workstation
 * Date: 6/6/17
 * Time: 12:42 PM
 */
include 'dependencies.php';
use Predis\Client;

// replace with HMM model
$redis = new Client([
    'scheme' => 'tcp',
    'host'   => 'localhost',
    'port'   => 6379,
]);

if (!$redis->exists("current")) {
    $redis->set('current', 'etisalat');
}

$current = $redis->get("current");

if ($current == "etisalat") {
    $name = 'etisalat';
    $current = $redis->set("current", "tm30");
} else {
    $name = 'tm30';
    $current = $redis->set("current", "etisalat");
}

$x = $agi->set_variable('CURRENT', $name);

$files = glob("/var/lib/asterisk/sounds/files/" . $name . '/*.wav');
$file = array_rand($files);
$_file = explode("/", $files[$file]);
$_files = explode(".", end($_file));
$file_path = "/var/lib/asterisk/sounds/files/" . $name . '/' . current($_files) . '.wav';
$play_path = "files/" . $name . '/' . current($_files);

if (!file_exists($file_path)) {
    $file_path = "/var/lib/asterisk/sounds/defaults/backup.wav";
    $play_path = "defaults/backup";
}

$agi->set_variable('FILE_PATH', $file_path);
$agi->set_variable('PLAY_PATH', $play_path);

return 200;