#!/usr/bin/php -q
<?php
/**
 * @package phpAGI_examples
 * @version 2.0
 * @param $text
 * @param string $filename
 */
date_default_timezone_set("Africa/Lagos");

include 'dependencies.php';
use Predis\Client;

$redis = new Client([
    'scheme' => 'tcp',
    'host'   => 'localhost',
    'port'   => 6379,
]);
$result = $agi->get_variable('CAMPAIGN_PATH');
$campaign_path = $result['data'];
$data = $redis->hgetall($campaign_path);

$text = preg_replace('/\s+/', '_', $data['play_path']);
$query =  $text;
$values = $redis->hgetall($query);

$unique_data = $agi->get_variable('UNIQUEID');
//$unique_id = $unique_data['data'];
$unique_id = $agi->get_variable('CDR(uniqueid)')['data'].'_'.$agi->get_variable('CDR(src)')['data'];

if ($values) {
    try {

        $subscribe_url = 'http://localhost:4043/elastic/cdr/subscribe';
        curl_setopt($ch, CURLOPT_URL, $subscribe_url);
        curl_setopt($ch, CURLOPT_POST, 1);

        $body = array(
            "uniqueid" => $unique_id,
            "userfield" => $data['id']
        );
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($body));
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_exec($ch);
    } catch (Exception $e) {
        echo $e;
    }
}

return 200;
?>