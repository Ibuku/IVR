<?php
/**
 * Created by PhpStorm.
 * User: stikks
 * Date: 9/29/16
 * Time: 12:39 PM
 */

namespace App\Controllers;
use App\Models\Files;
use App\Models\Campaign;

class FileController extends BaseController
{
    public function getPage($request, $response){

        $user = $this->auth->user();
        
        $files = json_encode(Files::all());

        return $this->view->render($response, 'templates/files.twig', [
            'files' => $files,
            'username' => $user->username
        ]);
    }

    public function deleteFile($request, $response, $args) {

        if (!isset($args['file_id'])) {
            return $response->withStatus(404);
        };

        $user = $this->auth->user();
        $match = ['username' => $user->username, 'id' => $args['file_id']];

        $file = Files::where($match)->first();

        if (!$file) {
            return $response->withStatus(404);
        };

        $campaign = Campaign::where('file_path', $file->file_path)->first();

        if ($campaign) {

            $campaign->update([
                'is_active' => false,
                'end_date' => date("Y-m-d")
            ]);

            $file_split = explode('/', $campaign->play_path);
            $file_name = end($file_split);

            try {
                rename($campaign->play_path, '/var/lib/asterisk/sounds/files/inactive/'. $campaign->username. '/'. $file_name);
            }
            catch (\Exception $e) {
            }
        }

        try {
            $play_split = explode('/', $file->file_path);
            $play_name = end($play_split);

            rename($file->file_path, realpath(__DIR__ . '/../..') . "/files/deleted/" . $play_name);
            $file->delete();
        }
        catch (\Exception $e) {
        }

        return $response->withStatus(200);
    }


}