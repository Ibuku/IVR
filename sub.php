#!/usr/local/bin/php -q
<?php
/**
 * @package phpAGI_examples
 * @version 2.0
 * @param $text
 * @param string $filename
 */
date_default_timezone_set("Africa/Lagos");

set_time_limit(30);
require('phpagi.php');
require('/opt/ivr/vendor/predis/predis/autoload.php');
use Predis\Client;

$agi = new AGI();
$ch = curl_init();
$sys_count = 1;

$data = $redis->hgetall($campaign_path);
$uniqueid = $agi->get_variable('CDR(uniqueid)')['data'].'_'.$agi->get_variable('CDR(src)')['data'];
$text = preg_replace('/\s+/', '_', $data['play_path']);
$query =  $text. ':'. $result;
$values = $redis->hgetall($query);

if ($values) {

    $subscribe_url = 'http://localhost:4043/elastic/cdr/subscribe';
    curl_setopt($ch, CURLOPT_URL, $subscribe_url);
    curl_setopt($ch, CURLOPT_POST, 1);

    $body = array(
        "uniqueid" => $uniqueid,
        "userfield" => $data['id']
    );
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($body));
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_exec($ch);
    $agi->noop();
}

return 200;
?>