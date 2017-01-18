<?php
/**
 * Created by PhpStorm.
 * User: codesmata
 * Date: 1/11/17
 * Time: 1:56 PM
 */

namespace App\Services;


use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use App\Services\CodeUtility as util;

class Queueing
{

    /**
     * @param $msg
     * Queueing Message Feature
     */
    function queueMessage($msg){
        $connection=new AMQPStreamConnection('localhost',5672,'guest','guest');
        $channel=$connection->channel();
        $channel->queue_declare('mails',false,false,false,false);

        $msg=new AMQPMessage(json_encode($msg));

        $channel->basic_publish($msg,'','mails');
        return true;
    }
}
