<?php
/**
 * Created by PhpStorm.
 * User: stikks
 * Date: 9/28/16
 * Time: 1:53 AM
 */
return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
        ],

        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => __DIR__ . '/../logs/app.log',
        ],
        'db' => [
            'driver' => 'pgsql',
//            'host' => 'localhost',
            'host' => 'localhost',
            'username' => 'postgres',
            'password' => 'postgres',
            'database' => 'ivr',
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci'
        ],
        "default_index" => "ivr",
        "DEFAULT_ACCOUNT" => "etisalat",
        'redis'=> [
            'scheme' => 'tcp',
            'host'   => 'localhost',
            'port'   => 6379,
        ],
        'es_url' => 'http://localhost:4043/elastic/',
        'REMOTE' => [
            'USERNAME' => 'root',
            'PASSWORD' => 'fileopen',
            'URL' => 'freepbx'
        ]
    ]
];
