<?php
/*/**
 * Created by PhpStorm.
 * User: stikks
 * Date: 9/29/16
 * Time: 12:08 PM
 */

namespace App\Controllers;

use App\Models\Files;
use App\Services\Converter;

require_once(__DIR__ . '/../../getID3/getid3/getid3.php');
use getID3;

class UploadController extends BaseController
{
    public function getPage($request, $response)
    {

        $error = null;

        $user = $this->auth->user();

        return $this->view->render($response, 'templates/upload.twig', [
            'error' => $error,
            'user' => $user
        ]);
    }

    public function postData($request, $response)
    {

        $user = $this->auth->user();

        if (($_FILES["advert"]["size"] < 20000000)) {
            if ($_FILES["advert"]["error"] > 0) {
                $error = $_FILES["advert"]["name"] . " is an invalid file, No audio codec provided";
                return $this->view->render($response, 'templates/upload.twig', [
                    'error' => $error,
                    'user' => $user
                ]);
            } else {
                if (file_exists(realpath(__DIR__ . '/../..') . "/files/" . $user->username . '/' . $_FILES["advert"]["name"])) {
                    $error = $_FILES["advert"]["name"] . " already exists. ";
                    return $this->view->render($response, 'templates/upload.twig', [
                        'error' => $error,
                        'user' => $user
                    ]);
                } elseif (preg_match('/\s/', $_FILES["advert"]["name"])) {
                    $error = $_FILES["advert"]["name"] . " contains whitespace. Edit filename and re-upload ";
                    return $this->view->render($response, 'templates/upload.twig', [
                        'error' => $error,
                        'user' => $user
                    ]);
                } else {
                    $temp_file = realpath(__DIR__ . '/../..') . "/files/" . $user->username . '/temp_' . $_FILES["advert"]["name"];

                    $move_result = move_uploaded_file($_FILES["advert"]["tmp_name"], $temp_file);

                    if (!$move_result) {
                        return $this->view->render($response, 'templates/upload.twig', [
                            'error' => $_FILES["advert"]["name"] . " not uploaded. ",
                            'user' => $user
                        ]);
                    }

                    $name = '' . preg_replace('/\s+/', '', explode(".", $_FILES["advert"]["name"])[0]) . '.wav';
                    $file_path = realpath(__DIR__ . '/../..') . "/files/" . $user->username . '/' . $name;
                    $conversion_cmd = '/usr/bin/ffmpeg -y -i ' . $temp_file . ' -ar 8000 -ac 1 ' . $file_path;

                    shell_exec($conversion_cmd);

                    if (!file_exists($file_path)) {
                        return $this->view->render($response, 'templates/upload.twig', [
                            'error' => $_FILES["advert"]["name"] . " not uploaded. Wrong Permission.",
                            'user' => $user
                        ]);
                    }

                    try {
                        unlink($temp_file);
                    } catch (\Exception $e) {
                        var_dump($e);
                    }

                    $getID3 = new getID3;
                    $info = $getID3->analyze($file_path);
                    $play_time = $info['playtime_string'];

                    list($mins, $secs) = explode(':', $play_time);

                    $hours = 0;

                    if ($mins > 60) {
                        $hours = intval($mins / 60);
                        $mins = $mins - $hours * 60;
                    }

                    $play_time = sprintf("%02d:%02d:%02d", $hours, $mins, $secs);

                    $name = $request->getParam('name');

                    if (!$name) {
                        $name = explode(".", $_FILES["advert"]["name"])[0] . '.wav';
                    }

                    $file = Files::create([
                        "username" => $user->username,
                        "file_path" => $file_path,
                        "size" => (float)($_FILES["advert"]["size"] / 1024),
                        "name" => $name,
                        "file_type" => "audio/x-wav",
                        "duration" => $play_time,
                        "description" => $request->getParam('description'),
                        "tag" => $request->getParam('tag')
                    ]);
                    chmod($file_path, 0777);
                    return $this->view->render($response, 'templates/upload.twig', [
                        "message" => $file->name . " was successfully uploaded",
                        'user' => $user
                    ]);
                }
            }
        } else {
            $error = $_FILES["advert"]["name"] . " is an invalid file, No audio codec provided";
            return $this->view->render($response, 'templates/upload.twig', [
                'error' => $error,
                'user' => $user
            ]);
        }

    }
}