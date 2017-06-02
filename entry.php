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
    $agi->stream_file("defaults/goodbye");
    return 200;
};

$campaign_path = preg_replace('/\s+/', '_', $file_path);
$data = $redis->hgetall($campaign_path);
$agi->set_variable("path", $data['play_path']);
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

[new]
exten => 39604,1,Verbose(1, Extension 39604)
exten => 39604,n,Progress()
exten => 39604,n,Answer()

;record call
exten => 39604,n,AGI(entry.php, 'http://localhost:4043/elastic/elasticsearch/cdr/create')

;play incorrect prompt
exten => 39604,n,Playback(incorrect)

;play selected file
exten => 39604,n,Playback(${play_path})

;record impression
exten => 39604,n,AGI(cdr.php)
exten => 39604,n,Hangup()
