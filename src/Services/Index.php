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
    public function __construct()
    {
        $settings_file = require(__DIR__ . '/../settings.php');
        $this->settings = $settings_file['settings'];
    }


    static public function index($type, $params = array())
    {

        $settings_file = require(__DIR__ . '/../settings.php');
        $settings = $settings_file['settings'];

        $ch = curl_init();
        $url = $settings['es_url'] . '/elasticsearch/' . $type . '/create';
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            http_build_query($params));

        // receive server response ...
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);

        curl_close($ch);

        return $server_output;
    }

    static public function update($type, $_id, $params = array())
    {

        $settings_file = require(__DIR__ . '/../settings.php');
        $settings = $settings_file['settings'];

        $ch = curl_init();
        $url = $settings['es_url'] . '/elasticsearch/' . $type . '/' . $_id . '/update';
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            http_build_query($params));

        // receive server response ...
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);

        curl_close($ch);

        return $server_output;
    }


    static public function get_data($type)
    {

        $settings_file = require(__DIR__ . '/../settings.php');
        $settings = $settings_file['settings'];
        $ch = curl_init();
        $url = $settings['es_url'] . '/elasticsearch/' . $type . '/get';
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);

        curl_close($ch);

        return $server_output;
    }

    static public function filterBy($type, $params = array())
    {
        $settings_file = require(__DIR__ . '/../settings.php');
        $settings = $settings_file['settings'];
        $ch = curl_init();
        $url = $settings['es_url'] . '/elasticsearch/' . $type . '/create';
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);

        curl_close($ch);

        return $server_output;
    }

    static public function save_redis($key, $params = array())
    {

        $settings_file = require(__DIR__ . '/../settings.php');
        $settings = $settings_file['settings'];
        $redisSettings = $settings['redis'];

        $redis = new Client([
            'scheme' => 'tcp',
            'host' => 'localhost',
            'port' => 6379,
        ]);

        $redis->hmset($string = preg_replace('/\s+/', '_', $key), $params);

        return true;
    }

}