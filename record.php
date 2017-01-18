#!/usr/local/bin/php -q
<?php
/**
 * @package phpAGI_examples
 * @version 2.0
 * @param $text
 * @param string $filename
 */
date_default_timezone_set("Africa/Lagos");

function append_line_to_limited_text_file($text, $filename='call_records') {
    $name = $filename. '.log';
    if (!file_exists($name)) {
        touch($name);
        chmod($name, 0777);
    }
    if (filesize($name) > 2*1024*1024) {
        $name = $filename. '_1.log';
        $k = 2;
        while (file_exists($name)) {
            $name = $filename. '_'. $k.'.log';
            $k++;
        };
        touch($name);
        chmod($name,0777);
    }
    file_put_contents($name, $text, FILE_APPEND | LOCK_EX);
}

set_time_limit(30);
require('phpagi.php');
require('/opt/ivr/vendor/predis/predis/autoload.php');
use Predis\Client;

$agi = new AGI();
$ch = curl_init();

$redis = new Client();

if (!$redis->exists("current")) {
    $redis->set('current', 'etisalat');
}
$current = $redis->get("current");

if ($current == "etisalat") {
    $name = 'etisalat';
    $current = $redis->set("current", "tm30");
} else {
    $name = 'tm30';
    $current = $redis->set("current", "etisalat");
}

$files = glob("/var/lib/asterisk/sounds/files/" . $name . '/*.wav');
$file = array_rand($files);

$_file = explode("/", $files[$file]);
$_files = explode(".", end($_file));
$file_path = "/var/lib/asterisk/sounds/files/" . $name . '/' . current($_files) . '.wav';

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
    return 200;
};

// retrieve campaign from redis
$data = $redis->hgetall($file_path);

// log call
$record = '[DATETIME:'.time().'][STATUS: Incoming Call][MSISDN:'.$agi->get_variable('CDR(src)')['data'].'][MSG: Incoming Call by '.$agi->get_variable('CDR(src)')['data'].'][FILE_PATH:'.$file_path.'][CAMPAIGN:'.$data['name'].']';
append_line_to_limited_text_file($record);

// save current play path to redis
$redis->set($agi->get_variable('CDR(uniqueid)')['data'].'_'.$agi->get_variable('CDR(src)')['data'], $file_path);

try {

//    $url = 'http://localhost:8079/cdr/create';
    $url = 'http://localhost:4043/elastic/elasticsearch/cdr/missing';
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);

    $body = array(
        "clid" => $agi->get_variable('CDR(clid)')['data'],
        "src" => $agi->get_variable('CDR(src)')['data'],
        "duration" => $agi->get_variable('CDR(duration)')['data'],
        "billsec" => $agi->get_variable('CDR(billsec)')['data'],
        "uniqueid" => $agi->get_variable('CDR(uniqueid)')['data'],
        "file_path" => $file_path
    );

    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($body));
    curl_exec($ch);
} catch (Exception $e) {
    echo $e;
}
return 200;
?>