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
use Predis\Client;

$log = new Logger('recordsLog');
$handler = new RotatingFileHandler('/home/stikks-workstation/Documents/projects/IVR/logs/activity.log', 0, Logger::INFO);
$log->pushHandler($handler);
