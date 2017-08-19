<?php
/**
 * Created by PhpStorm.
 * User: stikks-workstation
 * Date: 6/6/17
 * Time: 12:40 PM
 */
require('phpagi.php');
require('/opt/IVR/vendor/autoload.php');
use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;

$log = new Logger('recordsLog');
$handler = new RotatingFileHandler('/opt/IVR/logs/activity.log', 0, Logger::INFO);
$log->pushHandler($handler);

$agi = new AGI();
$ch = curl_init();