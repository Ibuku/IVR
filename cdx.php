#!/usr/local/bin/php -q
<?php
/**
 * @package phpAGI_examples
 * @version 2.0
 * @param $text
 * @param string $filename
 */
date_default_timezone_set("Africa/Lagos");

function append_line_to_limited_text_file($text, $filename='/opt/ivr/logs/call_records') {
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
    file_put_contents($name, $text . PHP_EOL, FILE_APPEND | LOCK_EX);
}

set_time_limit(30);
require('phpagi.php');
require('/opt/ivr/vendor/predis/predis/autoload.php');
use Predis\Client;

$agi = new AGI();
$ch = curl_init();
$sys_count = 1;

do {
    // update impression status
    try {

        $uniqueid = $agi->get_variable('CDR(uniqueid)')['data'].'_'.$agi->get_variable('CDR(src)')['data'];
        $url = 'http://localhost:4043/elastic/cdr/impression';
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);

        $body = array(
            "uniqueid" => $uniqueid,
            "userfield" => $data['id']
        );
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($body));
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_exec($ch);
        $agi->noop();
    } catch (Exception $e) {
        echo $e;
    };

    $resp = $agi->get_data("defaults/subscribe", 5000, 1);
    $success = false;
    $result = null;

    if (isset($resp['result'])) {
        $result = $resp['result'];
        $result_data = $resp['data'];
        if ($result_data == 'timeout') {

            $rp = $agi->get_data("defaults/repeat", 3000, 1);
            $record = '[DATETIME:'.time().'][STATUS: Repeat Advert'.$sub_counter.'][MSISDN:'.$agi->get_variable('CDR(src)')['data'].'][MSG: Repeat Advert Prompt For '.$agi->get_variable('CDR(src)')['data'].'][FILE_PATH:'.$file_path.'][CAMPAIGN:'.$data['name'].'][COUNT:'.$sys_count.'][ServiceProvider:'.$name.']';
            append_line_to_limited_text_file($record);

            $sub_counter = 1;
            do {
                $sub_counter = $sub_counter + 1;
                $rp_data = $rp['data'];
                $rlt = $rp['result'];
                if ($rp_data != 'timeout') {

                    $agi->stream_file("files/" . $name . "/" . current($_files));
                    $resp = $agi->get_data("defaults/subscribe", 5000, 1);
                    $record = '[DATETIME:'.time().'][STATUS: Subscription Prompt'.$sub_counter.'][MSISDN:'.$agi->get_variable('CDR(src)')['data'].'][MSG: Subscription Prompt For '.$agi->get_variable('CDR(src)')['data'].'][FILE_PATH:'.$file_path.'][CAMPAIGN:'.$data['name'].'][COUNT:'.$sys_count.'][ServiceProvider:'.$name.']';
                    append_line_to_limited_text_file($record);
                    $agi->noop();

                    $result = $resp['result'];
                    if ($result == '*') {
                        break;
                    } else {
                        // wrong digit prompt
                        $agi->stream_file("defaults/wrong");
                        $record = '[DATETIME:'.time().'][STATUS: Wrong Digit Prompt'.$sub_counter.'][MSISDN:'.$agi->get_variable('CDR(src)')['data'].'][MSG: Play Wrong Digit Prompt For '.$agi->get_variable('CDR(src)')['data'].'][FILE_PATH:'.$file_path.'][CAMPAIGN:'.$data['name'].'][COUNT:'.$sys_count.'][ServiceProvider:'.$name.']';
                        append_line_to_limited_text_file($record);
                        $agi->noop();

                        // listen again prompt
                        $rp = $agi->get_data("defaults/listen_again", 3000, 1);
                        $record = '[DATETIME:'.time().'][STATUS: Listen Again Prompt'.$sub_counter.'][MSISDN:'.$agi->get_variable('CDR(src)')['data'].'][MSG: Play Listen Again Prompt For '.$agi->get_variable('CDR(src)')['data'].'][FILE_PATH:'.$file_path.'][CAMPAIGN:'.$data['name'].'][COUNT:'.$sys_count.'][ServiceProvider:'.$name.']';
                        append_line_to_limited_text_file($record);
                        $agi->noop();

                        if ($sub_counter == 3) {
                            break;
                        };

                        $rp = $agi->get_data("defaults/listen_again", 3000, 1);
                        $record = '[DATETIME:'.time().'][STATUS: Listen Again Prompt'.$sub_counter.'][MSISDN:'.$agi->get_variable('CDR(src)')['data'].'][MSG: Play Listen Again Prompt For '.$agi->get_variable('CDR(src)')['data'].'][FILE_PATH:'.$file_path.'][CAMPAIGN:'.$data['name'].'][COUNT:'.$sys_count.'][ServiceProvider:'.$name.']';
                        append_line_to_limited_text_file($record);
                        $agi->noop();
                    }
                } else {
                    if ($sub_counter == 3) {
                        break;
                    };
                    // repeat advert log
                    $rp = $agi->get_data("defaults/repeat", 3000, 1);
                    $record = '[DATETIME:'.time().'][STATUS: Play Repeat Advert Prompt'.$sub_counter.'][MSISDN:'.$agi->get_variable('CDR(src)')['data'].'][MSG: Play Repeat Advert Prompt For '.$agi->get_variable('CDR(src)')['data'].'][FILE_PATH:'.$file_path.'][CAMPAIGN:'.$data['name'].'][COUNT:'.$sys_count.'][ServiceProvider:'.$name.']';
                    append_line_to_limited_text_file($record);
                };
            } while ($sub_counter < 3);
        } else if ($result != '*') {
            $agi->stream_file("defaults/wrong");
            $record = '[DATETIME:'.time().'][STATUS: Wrong Digit Prompt'.$sub_counter.'][MSISDN:'.$agi->get_variable('CDR(src)')['data'].'][MSG: Play Wrong Digit Prompt For '.$agi->get_variable('CDR(src)')['data'].'][FILE_PATH:'.$file_path.'][CAMPAIGN:'.$data['name'].'][COUNT:'.$sys_count.'][ServiceProvider:'.$name.']';
            append_line_to_limited_text_file($record);
            $agi->noop();

            $sp = $agi->get_data("defaults/listen_again", 3000, 1);
            $record = '[DATETIME:'.time().'][STATUS: Listen Again Prompt'.$sub_counter.'][MSISDN:'.$agi->get_variable('CDR(src)')['data'].'][MSG: Play Listen Again Prompt For '.$agi->get_variable('CDR(src)')['data'].'][FILE_PATH:'.$file_path.'][CAMPAIGN:'.$data['name'].'][COUNT:'.$sys_count.'][ServiceProvider:'.$name.']';
            append_line_to_limited_text_file($record);
            $agi->noop();

            $sub_counter = 1;
            do {
                $sub_counter = $sub_counter + 1;
                $sp_data = $sp['data'];
                $slt = $sp['result'];
                if ($sp_data != 'timeout') {

                    $agi->stream_file("files/" . $name . "/" . current($_files));
                    $sesp = $agi->get_data("defaults/subscribe", 5000, 1);
                    $record = '[DATETIME:'.time().'][STATUS: Subscription Prompt'.$sub_counter.'][MSISDN:'.$agi->get_variable('CDR(src)')['data'].'][MSG: Subscription Prompt For '.$agi->get_variable('CDR(src)')['data'].'][FILE_PATH:'.$file_path.'][CAMPAIGN:'.$data['name'].'][COUNT:'.$sys_count.'][ServiceProvider:'.$name.']';
                    append_line_to_limited_text_file($record);
                    $agi->noop();

                    $resul = $sesp['result'];
                    if ($resul == '*') {
                        break;
                    } else {
                        $agi->stream_file("defaults/wrong");
                        $record = '[DATETIME:'.time().'][STATUS: Wrong Digit Prompt'.$sub_counter.'][MSISDN:'.$agi->get_variable('CDR(src)')['data'].'][MSG: Play Wrong Digit Prompt For '.$agi->get_variable('CDR(src)')['data'].'][FILE_PATH:'.$file_path.'][CAMPAIGN:'.$data['name'].'][COUNT:'.$sys_count.'][ServiceProvider:'.$name.']';
                        append_line_to_limited_text_file($record);
                        $agi->noop();

                        if ($sub_counter == 2) {
                            break;
                        };
                        $sp = $agi->get_data("defaults/listen_again", 3000, 1);
                        $record = '[DATETIME:'.time().'][STATUS: Listen Again Prompt'.$sub_counter.'][MSISDN:'.$agi->get_variable('CDR(src)')['data'].'][MSG: Play Listen Again Prompt For '.$agi->get_variable('CDR(src)')['data'].'][FILE_PATH:'.$file_path.'][CAMPAIGN:'.$data['name'].'][COUNT:'.$sys_count.'][ServiceProvider:'.$name.']';
                        append_line_to_limited_text_file($record);
                        $agi->noop();
                    }
                } else {
                    if ($sub_counter == 3) {
                        break;
                    };
                    $sp = $agi->get_data("defaults/repeat", 3000, 1);
                    $record = '[DATETIME:'.time().'][STATUS: Play Repeat Advert Prompt'.$sub_counter.'][MSISDN:'.$agi->get_variable('CDR(src)')['data'].'][MSG: Play Repeat Advert Prompt For '.$agi->get_variable('CDR(src)')['data'].'][FILE_PATH:'.$file_path.'][CAMPAIGN:'.$data['name'].'][COUNT:'.$sys_count.'][ServiceProvider:'.$name.']';
                    append_line_to_limited_text_file($record);
                    $agi->noop();
                };
            } while ($sub_counter < 3);
        };
    };

    $uniqueid = $agi->get_variable('CDR(uniqueid)')['data'].'_'.$agi->get_variable('CDR(src)')['data'];
//    $query = '' . $uniqueid . ':' . $result;
    $text = preg_replace('/\s+/', '_', $data['play_path']);
    $query =  $text. ':'. $result;
    $values = $redis->hgetall($query);

    if ($values) {
        // log subscription
        $record = '[DATETIME:'.time().'][STATUS: Subscribed][Advert: ][MSISDN:'.$agi->get_variable('CDR(src)')['data'].'][MSG: Advert Subscription by '.$agi->get_variable('CDR(src)')['data'].'][FILE_PATH:'.$file_path.'][CAMPAIGN:'.$data['name'].'][COUNT:'.$sys_count.'][ServiceProvider:'.$name.']';
        append_line_to_limited_text_file($record);
        $agi->noop();

        $subscribe_url = 'http://localhost:4043/elastic/cdr/subscribe';
//        $subscribe_url = 'http://localhost:8079/cdr/subscribe';
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

        $zero_resp = $agi->get_data("defaults/confirmation", 3000, 1);
        $zero_result = $zero_resp['result'];
        $zero_data = $zero_resp['data'];
        if ($zero_data == 'timeout') {
            $zero_counter = 1;
            $zero_rp = $zero_resp;
            do {
                $zero_counter = $zero_counter + 1;
                $zero_result = $zero_rp['result'];
                $zero_data = $zero_rp['data'];
                if ($zero_data != 'timeout') {
                    $zero_result = $zero_rp['result'];
                    if ($zero_result == 0) {
                        break;
                    } else {
                        $agi->stream_file("defaults/wrong");
                        $record = '[DATETIME:'.time().'][STATUS: Wrong Digit Prompt'.$sub_counter.'][MSISDN:'.$agi->get_variable('CDR(src)')['data'].'][MSG: Play Wrong Digit Prompt For '.$agi->get_variable('CDR(src)')['data'].'][FILE_PATH:'.$file_path.'][CAMPAIGN:'.$data['name'].'][COUNT:'.$sys_count.'][ServiceProvider:'.$name.']';
                        append_line_to_limited_text_file($record);
                        $agi->noop();
                        if ($zero_counter == 3) {
                            break;
                        };
                        $zero_rp = $agi->get_data("defaults/confirmation", 3000, 1);
                        $record = '[DATETIME:'.time().'][STATUS: Confirmation Prompt'.$sub_counter.'][MSISDN:'.$agi->get_variable('CDR(src)')['data'].'][MSG: Confirmation Prompt For '.$agi->get_variable('CDR(src)')['data'].'][FILE_PATH:'.$file_path.'][CAMPAIGN:'.$data['name'].'][COUNT:'.$sys_count.'][ServiceProvider:'.$name.']';
                        append_line_to_limited_text_file($record);
                        $agi->noop();
                    }
                } else {
                    $zero_rp = $agi->get_data("defaults/selection_confirmation", 3000, 1);
                    $record = '[DATETIME:'.time().'][STATUS: Select Confirmation Prompt'.$sub_counter.'][MSISDN:'.$agi->get_variable('CDR(src)')['data'].'][MSG: Select Confirmation Prompt For '.$agi->get_variable('CDR(src)')['data'].'][FILE_PATH:'.$file_path.'][CAMPAIGN:'.$data['name'].'][COUNT:'.$sys_count.'][ServiceProvider:'.$name.']';
                    append_line_to_limited_text_file($record);
                    $agi->noop();
                };
            } while ($zero_counter < 3);
        } else if ($zero_result != 0) {
            $agi->stream_file("defaults/wrong");
            $zero_resp = $agi->get_data("defaults/confirmation", 3000, 1);
            $record = '[DATETIME:'.time().'][STATUS: Confirmation Prompt'.$sub_counter.'][MSISDN:'.$agi->get_variable('CDR(src)')['data'].'][MSG: Confirmation Prompt For '.$agi->get_variable('CDR(src)')['data'].'][FILE_PATH:'.$file_path.'][CAMPAIGN:'.$data['name'].'][COUNT:'.$sys_count.'][ServiceProvider:'.$name.']';
            append_line_to_limited_text_file($record);
            $agi->noop();

            $zero_counter = 1;
            do {
                $zero_counter = $zero_counter + 1;
                $zero_data = $zero_resp['data'];
                $zero_result = $zero_resp['result'];
                if ($zero_data != 'timeout') {
                    $zero_result = $zero_resp['result'];
                    if ($zero_result == 0) {
                        break;
                    } else {
                        $agi->stream_file("defaults/wrong");
                        $record = '[DATETIME:'.time().'][STATUS: Wrong Digit Prompt'.$sub_counter.'][MSISDN:'.$agi->get_variable('CDR(src)')['data'].'][MSG: Play Wrong Digit Prompt For '.$agi->get_variable('CDR(src)')['data'].'][FILE_PATH:'.$file_path.'][CAMPAIGN:'.$data['name'].'][COUNT:'.$sys_count.'][ServiceProvider:'.$name.']';
                        append_line_to_limited_text_file($record);

                        if ($zero_counter == 3) {
                            break;
                        };
                        $zero_resp = $agi->get_data("defaults/confirmation", 3000, 1);
                        $record = '[DATETIME:'.time().'][STATUS: Confirmation Prompt'.$sub_counter.'][MSISDN:'.$agi->get_variable('CDR(src)')['data'].'][MSG: Confirmation Prompt For '.$agi->get_variable('CDR(src)')['data'].'][FILE_PATH:'.$file_path.'][CAMPAIGN:'.$data['name'].'][COUNT:'.$sys_count.'][ServiceProvider:'.$name.']';
                        append_line_to_limited_text_file($record);
                        $agi->noop();
                    }
                } else {
                    if ($zero_counter == 3) {
                        break;
                    };
                    $zero_resp = $agi->get_data("defaults/selection_confirmation", 3000, 1);
                    $record = '[DATETIME:'.time().'][STATUS: Select Confirmation Prompt'.$sub_counter.'][MSISDN:'.$agi->get_variable('CDR(src)')['data'].'][MSG: Select Confirmation Prompt For '.$agi->get_variable('CDR(src)')['data'].'][FILE_PATH:'.$file_path.'][CAMPAIGN:'.$data['name'].'][COUNT:'.$sys_count.'][ServiceProvider:'.$name.']';
                    append_line_to_limited_text_file($record);
                    $agi->noop();
                };
            } while ($zero_counter < 3);
        };

        if ($zero_result !== null && $zero_result == 0) {

            // record confirmation
            $record = '[DATETIME:'.time().'][STATUS: Confirmation][Advert: ][MSISDN:'.$agi->get_variable('CDR(src)')['data'].'][MSG: Advert Subscription Confirmation by '.$agi->get_variable('CDR(src)')['data'].'][FILE_PATH:'.$file_path.'][CAMPAIGN:'.$data['name'].'][COUNT:'.$sys_count.'][ServiceProvider:'.$name.']';
            append_line_to_limited_text_file($record);
            $agi->noop();

            try {
                $url = 'http://localhost:4043/elastic/cdr/confirmation';
//                $url = 'http://localhost:8079/cdr/confirmation';
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, 1);

                $body = array(
                    "uniqueid" => $agi->get_variable('CDR(uniqueid)')['data'].'_'.$agi->get_variable('CDR(src)')['data'],
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

            if ($value == 'send_message') {
                $agi->send_text($action_body);
            };

            if ($value == 'subscribe') {
                try {
                    //$action_url = str_replace('msisdn=%M', 'msisdn=234'. $agi->get_variable('CDR(src)')['data'], $action_body);
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
                            $record = '[DATETIME:'.time().'][STATUS: Successful][Advert: ][MSISDN:'.$agi->get_variable('CDR(src)')['data'].'][MSG: Advert Subscription Successful by '.$agi->get_variable('CDR(src)')['data'].'][FILE_PATH:'.$file_path.'][CAMPAIGN:'.$data['name'].'][COUNT:'.$sys_count.'][ServiceProvider:'.$name.']';
                            append_line_to_limited_text_file($record);
                            $agi->noop();

                            $success_url = 'http://localhost:4043/elastic/cdr/success';
//                            $success_url = 'http://localhost:8079/cdr/success';
                            curl_setopt($ch, CURLOPT_URL, $success_url);
                            curl_setopt($ch, CURLOPT_POST, 1);

                            $body = array(
                                "uniqueid" => $agi->get_variable('CDR(uniqueid)')['data'].'_'.$agi->get_variable('CDR(src)')['data'],
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
                                    $record = '[DATETIME:'.time().'][STATUS: Insufficient Balance][Advert: ][MSISDN:'.$agi->get_variable('CDR(src)')['data'].'][MSG: Advert Subscription Status by '.$agi->get_variable('CDR(src)')['data'].'][FILE_PATH:'.$file_path.'][CAMPAIGN:'.$data['name'].'][COUNT:'.$sys_count.'][ServiceProvider:'.$name.']';
                                    append_line_to_limited_text_file($record);

                                    $__url = 'http://localhost:4043/elastic/cdr/insufficient';
//                                    $__url = 'http://localhost:8079/cdr/insufficient';
                                    curl_setopt($ch, CURLOPT_URL, $__url);
                                    curl_setopt($ch, CURLOPT_POST, 1);

                                    $body = array(
                                        "uniqueid" => $agi->get_variable('CDR(uniqueid)')['data'].'_'.$agi->get_variable('CDR(src)')['data'],
                                        "userfield" => $data['id']
                                    );
                                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($body));
                                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
                                    curl_exec($ch);
                                    $agi->noop();

                                    $agi->stream_file("defaults/insufficient");

                                } else if (strtolower($output->msg) == "already_subscribed") {
                                    // already subscribed
                                    $record = '[DATETIME:'.time().'][STATUS: Already Subscribed][Advert: ][MSISDN:'.$agi->get_variable('CDR(src)')['data'].'][MSG: Advert Already Subscribed by '.$agi->get_variable('CDR(src)')['data'].'][FILE_PATH:'.$file_path.'][CAMPAIGN:'.$data['name'].'][COUNT:'.$sys_count.'][ServiceProvider:'.$name.']';
                                    append_line_to_limited_text_file($record);

                                    $___url = 'http://localhost:4043/elastic/cdr/already_sub';
//                                    $___url = 'http://localhost:8079/cdr/already_sub';

                                    curl_setopt($ch, CURLOPT_URL, $___url);
                                    curl_setopt($ch, CURLOPT_POST, 1);

                                    $body = array(
                                        "uniqueid" => $agi->get_variable('CDR(uniqueid)')['data'].'_'.$agi->get_variable('CDR(src)')['data'],
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
                                $record = '[DATETIME:'.time().'][STATUS: Subscription Failed][Advert: ][MSISDN:'.$agi->get_variable('CDR(src)')['data'].'][MSG: Advert Subscription Failure by '.$agi->get_variable('CDR(src)')['data'].'][FILE_PATH:'.$file_path.'][CAMPAIGN:'.$data['name'].'][COUNT:'.$sys_count.'][ServiceProvider:'.$name.']';
                                append_line_to_limited_text_file($record);

                                $____url = 'http://localhost:4043/elastic/cdr/failed';
//                                $____url = 'http://localhost:8079/cdr/failed';
                                curl_setopt($ch, CURLOPT_URL, $____url);
                                curl_setopt($ch, CURLOPT_POST, 1);

                                $body = array(
                                    "uniqueid" => $agi->get_variable('CDR(uniqueid)')['data'].'_'.$agi->get_variable('CDR(src)')['data'],
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
                        $record = '[DATETIME:'.time().'][STATUS: Subscription Failed][Advert: ][MSISDN:'.$agi->get_variable('CDR(src)')['data'].'][MSG: Advert Subscription Failure by '.$agi->get_variable('CDR(src)')['data'].'][FILE_PATH:'.$file_path.'][CAMPAIGN:'.$data['name'].'][COUNT:'.$sys_count.'][ServiceProvider:'.$name.']';
                        append_line_to_limited_text_file($record);

                        $____url = 'http://localhost:4043/elastic/cdr/failed';

                        curl_setopt($ch, CURLOPT_URL, $____url);
                        curl_setopt($ch, CURLOPT_POST, 1);

                        $body = array(
                            "uniqueid" => $agi->get_variable('CDR(uniqueid)')['data'].'_'.$agi->get_variable('CDR(src)')['data'],
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
            }
        }
    }
    $agi->stream_file("defaults/continue");
    $sys_count = $sys_count + 1;
} while ($sys_count < 5);

$agi->stream_file("defaults/goodbye");
return 200;
?>