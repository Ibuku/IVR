#!/usr/bin/php -q
<?php
/**
 * Created by PhpStorm.
 * User: stikks-workstation
 * Date: 6/20/17
 * Time: 11:32 AM
 */
include 'dependencies.php';
use Predis\Client;

$redis = new Client();
$result = $agi->get_variable('CAMPAIGN_PATH');
$campaign_path = $result['data'];

$data = $redis->hgetall($campaign_path);
$text = preg_replace('/\s+/', '_', $data['play_path']);
$query =  $text. ':*';
$values = $redis->hgetall($query);

$unique_data = $agi->get_variable('UNIQUEID');
$unique_id = $unique_data['data'];

try {
    $url = 'http://localhost:4043/elastic/cdr/confirmation';
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);

    $body = array(
        "uniqueid" => $unique_id,
        "userfield" => $data['id']
    );
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($body));
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_exec($ch);
    $agi->noop();
} catch (Exception $e) {
    echo $e;
};

$action_body = $values['body'];
$value = $values['value'];
$parameter = $values['parameter'];

try {
    $_url = "http://172.16.24.2/ivr/index.php";
    curl_setopt($ch, CURLOPT_URL, $_url);
    curl_setopt($ch, CURLOPT_POST, 1);
    $body = array(
        "url" => $action_body,
        "msisdn" => '234' . $agi->get_variable('CDR(src)')['data'],
        "parameter" => $parameter
    );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($body));
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
    $out = curl_exec($ch);
    $agi->noop();

    $info = curl_getinfo($ch);
    if (isset($info['http_code'])) {
        $code = $info['http_code'];
        if ($code == 200) {
            // confirming subscription
            $record = '[DATETIME:' . time() . '][STATUS: Successful][Advert: ][MSISDN:' . $agi->get_variable('CDR(src)')['data'] . '][MSG: Advert Subscription Successful by ' . $agi->get_variable('CDR(src)')['data'] . '][FILE_PATH:' . $file_path . '][CAMPAIGN:' . $data['name'] . '][COUNT:' . $sys_count . '][ServiceProvider:' . $name . ']';
            $agi->noop();

            $success_url = 'http://localhost:4043/elastic/cdr/success';
            curl_setopt($ch, CURLOPT_URL, $success_url);
            curl_setopt($ch, CURLOPT_POST, 1);

            $body = array(
                "uniqueid" => $agi->get_variable('CDR(uniqueid)')['data'] . '_' . $agi->get_variable('CDR(src)')['data'],
                "userfield" => $data['id']
            );
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($body));
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
            curl_exec($ch);
            $agi->noop();

            $agi->stream_file("defaults/success");
        } else if ($code == 202) {
            try {
                $output = json_decode($out);
                // insufficient balance
                if (strtolower($output->msg) == "insufficient_balance") {
                    $record = '[DATETIME:' . time() . '][STATUS: Insufficient Balance][Advert: ][MSISDN:' . $agi->get_variable('CDR(src)')['data'] . '][MSG: Advert Subscription Status by ' . $agi->get_variable('CDR(src)')['data'] . '][FILE_PATH:' . $file_path . '][CAMPAIGN:' . $data['name'] . '][COUNT:' . $sys_count . '][ServiceProvider:' . $name . ']';
                    append_line_to_limited_text_file($record);

                    $__url = 'http://localhost:4043/elastic/cdr/insufficient';
                    curl_setopt($ch, CURLOPT_URL, $__url);
                    curl_setopt($ch, CURLOPT_POST, 1);

                    $body = array(
                        "uniqueid" => $agi->get_variable('CDR(uniqueid)')['data'] . '_' . $agi->get_variable('CDR(src)')['data'],
                        "userfield" => $data['id']
                    );
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($body));
                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
                    curl_exec($ch);
                    $agi->noop();

                    $agi->stream_file("defaults/insufficient");

                } else if (strtolower($output->msg) == "already_subscribed") {
                    // already subscribed
                    $record = '[DATETIME:' . time() . '][STATUS: Already Subscribed][Advert: ][MSISDN:' . $agi->get_variable('CDR(src)')['data'] . '][MSG: Advert Already Subscribed by ' . $agi->get_variable('CDR(src)')['data'] . '][FILE_PATH:' . $file_path . '][CAMPAIGN:' . $data['name'] . '][COUNT:' . $sys_count . '][ServiceProvider:' . $name . ']';
                    append_line_to_limited_text_file($record);

                    $___url = 'http://localhost:4043/elastic/cdr/already_sub';

                    curl_setopt($ch, CURLOPT_URL, $___url);
                    curl_setopt($ch, CURLOPT_POST, 1);

                    $body = array(
                        "uniqueid" => $agi->get_variable('CDR(uniqueid)')['data'] . '_' . $agi->get_variable('CDR(src)')['data'],
                        "userfield" => $data["id"]
                    );
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($body));
                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
                    curl_exec($ch);
                    $agi->noop();

                    $agi->stream_file("defaults/already_subscribed");
                }
            } catch (Exception $e) {
                // subscription failed
                $record = '[DATETIME:' . time() . '][STATUS: Subscription Failed][Advert: ][MSISDN:' . $agi->get_variable('CDR(src)')['data'] . '][MSG: Advert Subscription Failure by ' . $agi->get_variable('CDR(src)')['data'] . '][FILE_PATH:' . $file_path . '][CAMPAIGN:' . $data['name'] . '][COUNT:' . $sys_count . '][ServiceProvider:' . $name . ']';

                $____url = 'http://localhost:4043/elastic/cdr/failed';
                curl_setopt($ch, CURLOPT_URL, $____url);
                curl_setopt($ch, CURLOPT_POST, 1);

                $body = array(
                    "uniqueid" => $unique_id,
                    "userfield" => $data['id']
                );
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($body));
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
                curl_exec($ch);
                $agi->noop();

                $agi->stream_file("defaults/failed");
            }
        }
    } else {
        // subscription failed
        $record = '[DATETIME:' . time() . '][STATUS: Subscription Failed][Advert: ][MSISDN:' . $agi->get_variable('CDR(src)')['data'] . '][MSG: Advert Subscription Failure by ' . $agi->get_variable('CDR(src)')['data'] . '][FILE_PATH:' . $file_path . '][CAMPAIGN:' . $data['name'] . '][COUNT:' . $sys_count . '][ServiceProvider:' . $name . ']';

        $____url = 'http://localhost:4043/elastic/cdr/failed';

        curl_setopt($ch, CURLOPT_URL, $____url);
        curl_setopt($ch, CURLOPT_POST, 1);

        $body = array(
            "uniqueid" => $unique_id,
            "userfield" => $data['id']
        );
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($body));
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
        curl_exec($ch);
        $agi->noop();

        $agi->stream_file("defaults/failed");
    }
} catch (Exception $e) {
    echo $e;
}

return 200;