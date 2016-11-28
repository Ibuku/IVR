<?php
/**
 * Created by PhpStorm.
 * User: stikks
 * Date: 9/28/16
 * Time: 8:14 AM
 */

namespace App\Controllers;

use App\Services\Index;
use App\Models\Settings;
use App\Models\Files;


class SettingsController extends BaseController
{

    public function getPage($request, $response)
    {
        $files = Files::where('tag', 'prompt')->get();

        $setting = Settings::first();

        return $this->view->render($response, 'templates/forms/settings.twig', [
            'files' => $files,
            'setting' => $setting
        ]);
    }

    public function postData($request, $response)
    {

        $settings = Settings::create([
            'advert_limit' => $request->getParam('advert_limit'),
            'incorrect_path' => $request->getParam('incorrect_path'),
            'repeat_path' => $request->getParam('repeat_path'),
            'confirmation_path' => $request->getParam('confirmation_path')
        ]);

        Index::index('settings', [
                'advert_limit' => $settings->advert_limit,
                'incorrect_path' => $settings->incorrect_path,
                'repeat_path' => $settings->repeat_path,
                'confirmation_path' => $settings->confirmation_path
            ]
        );

        $incorrect_command = 'cp '. $settings->incorrect_path. ' '. "/var/lib/asterisk/sounds/defaults/incorrect.wav";
        shell_exec($incorrect_command);

        $repeat_command = 'cp '. $settings->repeat_path. ' '. "/var/lib/asterisk/sounds/defaults/repeat.wav";
        shell_exec($repeat_command);

        $con_command = 'cp '. $settings->confirmation_path. ' '. "/var/lib/asterisk/sounds/defaults/confirmation.wav";
        shell_exec($con_command);

        return $response->withRedirect($this->router->pathFor('settings'));

    }
}