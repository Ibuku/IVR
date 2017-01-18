<?php
/**
 * Created by PhpStorm.
 * User: stikks
 * Date: 1/11/17
 * Time: 2:19 PM
 */

require 'vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;


$connection =new AMQPStreamConnection('localhost',5672,'guest','guest');
$channel=$connection->channel();
$channel->queue_declare('cdr',false,false,false,false);

$callBack=function($msg){
    var_dump($msg->getBody());
};

echo '[*] Waiting for message exit wit CTRL+C'."\n";

$channel->basic_consume('cdr','',false,true,false,false,$callBack);
while(count($channel->callbacks)){
    $channel->wait();
}

$channel->close();
$connection->close();