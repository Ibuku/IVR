<?php
/**
 * Created by PhpStorm.
 * User: stikks
 * Date: 8/4/16
 * Time: 12:55 PM
 */

session_start();

require __DIR__ . '/../vendor/autoload.php';

$settings = require __DIR__ . '/../src/settings.php';

$app = new \Slim\App($settings);

require __DIR__ . '/../src/dependencies.php';

require __DIR__ . '/../src/routes.php';


