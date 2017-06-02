#!/usr/bin/php -q
<?php
require("/var/lib/asterisk/agi-bin/phpagi.php");
require __DIR__ . '/var/www/html/marketing/vendor/predis/predis/autoload.php';
use Predis\Client;

set_time_limit(20);
$agi = new AGI();
$agi->answer();

$redis = new Client();

if (!$redis->exists("current")) {
    $redis->set('current', 'etisalat');
}

$current = $redis->get("current");

if ($current == "etisalat") {
    $name = 'etisalat';
    $current = $redis->set("current", "tm30");
}
else {
    $name = 'tm30';
    $current = $redis->set("current", "etisalat");
}

$files = glob("/var/lib/asterisk/sounds/files/" .$name);
$file = array_rand($files);
$_file = explode("/", $files[$file]);
$_files = explode(".", end($_file));
$agi->stream_file("advert/".current($_files));
#print("advert/".current($_files));
?>
