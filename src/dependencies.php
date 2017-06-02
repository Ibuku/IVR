<?php
/**
 * Created by PhpStorm.
 * User: stikks
 * Date: 9/28/16
 * Time: 1:53 AM
 */

use Respect\Validation\Validator as Val;
use App\Auth\Auth;

$container = $app->getContainer();

$capsule = new \Illuminate\Database\Capsule\Manager;

$capsule->addConnection($container['settings']['db']);

$capsule->setAsGlobal();

$capsule->bootEloquent();

// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], Monolog\Logger::DEBUG));
    return $logger;
};

$container['db'] = function ($c) use ($capsule) {

    return $capsule;
};

$container['auth'] = function ($c) {
    return new Auth;
};

$container['view'] = function ($c) {
    $view = new \Slim\Views\Twig(__DIR__ . '/../resources', [
        'cache' => false,
    ]);
    $view->addExtension(new \Slim\Views\TwigExtension(
        $c->router,
        $c->request->getUri()
    ));

    $view->getEnvironment()->addGlobal('auth', [
        'check' => $c->auth->check(),
//        'user' => $c->auth->user()
    ]);

//    $view->getEnvironment()->addGlobal('flash', $c->flash);

    return $view;
};

$container['validator'] = function ($c) {
    return new App\Validation\Validator;
};

//$container['csrf'] = function ($c) {
//    return new Slim\Csrf\Guard;
//};

//$app->add($container->get('csrf'));

$container["IndexController"] = function ($c) {
    return new \App\Controllers\IndexController($c);
};

$container["AuthController"] = function ($c) {
    return new \App\Controllers\AuthController($c);
};

$container["LoginController"] = function ($c) {
    return new \App\Controllers\LoginController($c);
};

$container["CampaignController"] = function ($c) {
    return new \App\Controllers\CampaignController($c);
};

$container["SettingsController"] = function ($c) {
    return new \App\Controllers\SettingsController($c);
};

$container["UploadController"] = function ($c) {
    return new \App\Controllers\UploadController($c);
};

$container["FileController"] = function ($c) {
    return new \App\Controllers\FileController($c);
};

$container["ReportController"] = function ($c) {
    return new \App\Controllers\ReportController($c);
};

$container["AccountController"] = function ($c) {
    return new \App\Controllers\AccountController($c);
};

$app->add(new \App\Middleware\ValidationErrorsMiddleware($container));
$app->add(new \App\Middleware\OldInputMiddleware($container));
//$app->add(new \App\Middleware\CsrfMiddleware($container));

Val::with('App\\Validation\\Rules\\');