<?php
require __DIR__ . '/vendor/autoload.php';
use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;

$log = new Logger('recordsLog');
$handler = new RotatingFileHandler('/home/stikks-workstation/Documents/projects/IVR/logs/activity.log', 0, Logger::INFO);
$log->pushHandler($handler);

while (1) {
    $log->info("Will have {food} for {meal}", array('food' => $food, 'meal' => $meal));
}

?>

