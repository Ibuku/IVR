<?php
require __DIR__ . '/vendor/autoload.php';
use Predis\Client;

$redis = new Client();

$key = 'linus';
$redis->hmset($key, [
    'age' => 44,
    'country' => 'finland',
    'occupation' => 'software engineer',
    'reknown' => 'linux kernel',
]);

$data = $redis->hgetall($key);
print_r($data); // returns all key-value that belongs to the hash
var_dump($data['age']);
/*
    [
        'age' => 44,
        'country' => 'finland',
        'occupation' => 'software engineer',
        'reknown' => 'linux kernel',
    ]
*/
