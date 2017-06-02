<?php
/**
 * Created by PhpStorm.
 * User: stikks
 * Date: 10/19/16
 * Time: 6:13 PM
 */

namespace App\Services;
use Predis\Client;

class Index
{
    static public function index($type, $params=array()) {

        $ch = curl_init();
        $url = 'http://localhost:4043/elastic/elasticsearch/'. $type.'/create';
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
                  http_build_query($params));
        
        // receive server response ...
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec ($ch);

        curl_close ($ch);

        return $server_output;
    }

    static public function update($type, $_id, $params=array()) {

        $ch = curl_init();
        $url = 'http://localhost:4043/elastic/elasticsearch/'. $type. '/'.  $_id. '/update';
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            http_build_query($params));

        // receive server response ...
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec ($ch);

        curl_close ($ch);

        return $server_output;
    }


    static public function get_data($type) {
        $ch = curl_init();
        $url = 'http://localhost:4043/elastic/elasticsearch/'. $type.'/get';
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec ($ch);

        curl_close ($ch);

        return $server_output;
    }

    static public function filterBy($type, $params=array()) {
        $ch = curl_init();
        $url = 'http://localhost:4043/elastic/elasticsearch/'. $type.'/create';
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec ($ch);

        curl_close ($ch);

        return $server_output;
    }

    static public function save_redis($key, $params=array()) {

        $redis = new Client();

        $redis->hmset($string = preg_replace('/\s+/', '_', $key), $params);

        return true;
    }

}