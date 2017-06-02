<?php
/**
 * Created by PhpStorm.
 * User: stikks-workstation
 * Date: 11/23/16
 * Time: 9:23 AM
 */

if (!isset($_POST['url'])) {
    http_response_code(404);
    exit();
}

$_url = $_POST['url'];
$ch = curl_init();

$url = rawurldecode($_url);

curl_setopt($ch, CURLOPT_URL, $url);

// receive server response ...
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$server_output = curl_exec ($ch);

curl_close ($ch);

http_response_code(200);
exit();
