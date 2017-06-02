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
        $user = $this->auth->user();

        $files = Files::where('tag', 'prompt')->get();

        $setting = Settings::where('default_settings', true)->first();

        return $this->view->render($response, 'templates/forms/settings.twig', [
            'user' => $user,
            'files' => $files,
            'setting' => $setting
        ]);
    }

    public function postData($request, $response)
    {

        $settings = Settings::first();

        if ($settings) {
            $settings->update([
                'advert_limit' => $request->getParam('advert_limit'),
                'incorrect_path' => $request->getParam('incorrect_path'),
                'repeat_path' => $request->getParam('repeat_path'),
                'confirmation_path' => $request->getParam('confirmation_path'),
                'goodbye_path' => $request->getParam('goodbye_path'),
                'wrong_path' => $request->getParam('wrong_path'),
                'success_path' => $request->getParam('success_path'),
                'no_selection_path' => $request->getParam('no_selection_path'),
                'selection_confirmation_path' => $request->getParam('selection_confirmation_path'),
                'default_settings' => true,
                'subscription_failure_path' => $request->getParam('subscription_failure_path'),
                'continue_path' => $request->getParam('continue_path')
            ]);
        } else {
            $settings = Settings::create([
                'advert_limit' => $request->getParam('advert_limit'),
                'incorrect_path' => $request->getParam('incorrect_path'),
                'repeat_path' => $request->getParam('repeat_path'),
                'confirmation_path' => $request->getParam('confirmation_path'),
                'goodbye_path' => $request->getParam('goodbye_path'),
                'wrong_path' => $request->getParam('wrong_path'),
                'success_path' => $request->getParam('success_path'),
                'no_selection_path' => $request->getParam('no_selection_path'),
                'selection_confirmation_path' => $request->getParam('selection_confirmation_path'),
                'default_settings' => true,
                'subscription_failure_path' => $request->getParam('subscription_failure_path'),
                'continue_path' => $request->getParam('continue_path')
            ]);
        }

        $incorrect_command = 'cp ' . $settings->incorrect_path . ' ' . "/var/lib/asterisk/sounds/defaults/incorrect.wav";
        shell_exec($incorrect_command);

        $repeat_command = 'cp ' . $settings->repeat_path . ' ' . "/var/lib/asterisk/sounds/defaults/repeat.wav";
        shell_exec($repeat_command);

        $con_command = 'cp ' . $settings->confirmation_path . ' ' . "/var/lib/asterisk/sounds/defaults/confirmation.wav";
        shell_exec($con_command);

        $settings_command = 'cp ' . $settings->goodbye_path . ' ' . "/var/lib/asterisk/sounds/defaults/goodbye.wav";
        shell_exec($settings_command);

        $wrong_command = 'cp ' . $settings->wrong_path . ' ' . "/var/lib/asterisk/sounds/defaults/wrong.wav";
        shell_exec($wrong_command);

        $success_command = 'cp ' . $settings->success_path . ' ' . "/var/lib/asterisk/sounds/defaults/success.wav";
        shell_exec($success_command);

        $no_selection_command = 'cp ' . $settings->no_selection_path . ' ' . "/var/lib/asterisk/sounds/defaults/no_selection.wav";
        shell_exec($no_selection_command);

        $select_command = 'cp ' . $settings->selection_confirmation_path . ' ' . "/var/lib/asterisk/sounds/defaults/selection_confirmation.wav";
        shell_exec($select_command);

        $failure_command = 'cp ' . $settings->subscription_failure_path . ' ' . "/var/lib/asterisk/sounds/defaults/failure.wav";
        shell_exec($failure_command);

        $continue_command = 'cp ' . $settings->continue_path . ' ' . "/var/lib/asterisk/sounds/defaults/continue.wav";
        shell_exec($continue_command);

        Index::index('settings', [
                'id' => $settings->id,
                'advert_limit' => $settings->advert_limit,
                'incorrect_path' => '/var/lib/asterisk/sounds/defaults/incorrect.wav',
                'repeat_path' => "/var/lib/asterisk/sounds/defaults/repeat.wav",
                'confirmation_path' => "/var/lib/asterisk/sounds/defaults/confirmation.wav",
                'goodbye_path' => "/var/lib/asterisk/sounds/defaults/goodbye.wav",
                'wrong_path' => $settings->wrong_path,
                'success_path' => $settings->success_path,
                'no_selection_path' => $settings->no_selection_path,
                'selection_confirmation_path' => $settings->selection_confirmation_path,
                'default_settings' => $settings->default,
                'subscription_failure_path' => $settings->subscription_failure_path,
                'continue_path' => $settings->continue_path
            ]
        );

        return $response->withRedirect($this->router->pathFor('settings'));

    }
}