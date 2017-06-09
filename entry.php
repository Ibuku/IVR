#!/usr/bin/php -q
<?php
/**
 * Created by PhpStorm.
 * User: stikks-workstation
 * Date: 1/20/17
 * Time: 12:41 PM
 * Objective: Record Incoming Call into Elasticsearch and a log file
 */

date_default_timezone_set("Africa/Lagos");

set_time_limit(30);
include 'dependencies.php';

$file_path = $agi->get_variable('FILE_PATH');
$campaign_path = preg_replace('/\s+/', '_', $file_path);
$data = $redis->hgetall($campaign_path);

// record missing audio call
if (!file_exists($file_path)) {
    if ($name == 'etisalat') {
        $agi->stream_file("etisalat_backup");
    } else {
        $agi->stream_file("tm30_backup");
    }
    try {
        $url = 'http://localhost:4043/elastic/elasticsearch/cdr/missing';
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);

        $body = array(
            "clid" => $agi->get_variable('CDR(clid)')['data'],
            "src" => $agi->get_variable('CDR(src)')['data'],
            "duration" => $agi->get_variable('CDR(duration)')['data'],
            "billsec" => $agi->get_variable('CDR(billsec)')['data'],
            "uniqueid" => $agi->get_variable('CDR(uniqueid)')['data'],
            "name" => $name
        );

        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($body));
        curl_exec($ch);
        curl_close($ch);
    } catch (Exception $e) {
        echo $e;
    }
    $agi->stream_file("defaults/goodbye");
    return 200;
};

try {

    $url = 'http://localhost:4043/elastic/elasticsearch/cdr/create';
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);

    $body = array(
        "clid" => $agi->get_variable('CDR(clid)')['data'],
        "src" => $agi->get_variable('CDR(src)')['data'],
        "duration" => $agi->get_variable('CDR(duration)')['data'],
        "billsec" => $agi->get_variable('CDR(billsec)')['data'],
        "uniqueid" => $agi->get_variable('CDR(uniqueid)')['data'].'_'.$agi->get_variable('CDR(src)')['data'],
        "campaign_name" => $data['name'],
        "file_path" => $data['play_path'],
        "userfield" => $data['id']
    );

    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($body));
    curl_exec($ch);
} catch (Exception $e) {
    echo $e;
}

return 200;
?>
