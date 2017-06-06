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
        $user = $this->auth->user();

        if ($settings) {
            $settings->update([
                'advert_limit' => $request->getParam('advert_limit'),
                'incorrect_path' => $request->getParam('incorrect_path'),
                'repeat_path' => $request->getParam('repeat_path'),
                'confirmation_path' => $request->getParam('confirmation_path'),
                'continue_path' => $request->getParam('continue_path'),
                'wrong_path' => $request->getParam('wrong_path'),
                'no_selection_path' => $request->getParam('no_selection_path'),
                'selection_confirmation_path' => $request->getParam('selection_confirmation_path'),
                'subscription_failure_path' => $request->getParam('subscription_failure_path'),
                'success_path' => $request->getParam('success_path'),
                'goodbye_path' => $request->getParam('goodbye_path'),
                'default_settings' => true
            ]);
        } else {
            $settings = Settings::create([
                'advert_limit' => $request->getParam('advert_limit'),
                'incorrect_path' => $request->getParam('incorrect_path'),
                'repeat_path' => $request->getParam('repeat_path'),
                'confirmation_path' => $request->getParam('confirmation_path'),
                'continue_path' => $request->getParam('continue_path'),
                'wrong_path' => $request->getParam('wrong_path'),
                'no_selection_path' => $request->getParam('no_selection_path'),
                'selection_confirmation_path' => $request->getParam('selection_confirmation_path'),
                'subscription_failure_path' => $request->getParam('subscription_failure_path'),
                'success_path' => $request->getParam('success_path'),
                'goodbye_path' => $request->getParam('goodbye_path'),
                'default_settings' => true
            ]);
        }

        $incorrect_copy = copy($settings->incorrect_path, "/var/lib/asterisk/sounds/defaults/incorrect.wav");

        if (!$incorrect_copy) {
            return $this->view->render($response, 'templates/forms/settings.twig', [
                'user' => $user,
                'error' => 'Incorrect prompt not saved',
                'files' => Files::where('tag', 'prompt')->get(),
                'setting' => $settings
            ]);
        }

        $repeat_copy = copy($settings->repeat_path, "/var/lib/asterisk/sounds/defaults/repeat.wav");

        if (!$repeat_copy) {
            return $this->view->render($response, 'templates/forms/settings.twig', [
                'user' => $user,
                'error' => 'Repeat prompt not saved',
                'files' => Files::where('tag', 'prompt')->get(),
                'setting' => $settings
            ]);
        }

        $con_copy = copy($settings->confirmation_path, "/var/lib/asterisk/sounds/defaults/confirmation.wav");

        if (!$con_copy) {
            return $this->view->render($response, 'templates/forms/settings.twig', [
                'user' => $user,
                'error' => 'Confirmation prompt not saved',
                'files' => Files::where('tag', 'prompt')->get(),
                'setting' => $settings
            ]);
        }

        $wrong_copy = copy($settings->wrong_path, "/var/lib/asterisk/sounds/defaults/wrong.wav");

        if (!$wrong_copy) {
            return $this->view->render($response, 'templates/forms/settings.twig', [
                'user' => $user,
                'error' => 'Wrong Selection prompt not saved',
                'files' => Files::where('tag', 'prompt')->get(),
                'setting' => $settings
            ]);
        }

        $no_selection_copy = copy($settings->no_selection_path, "/var/lib/asterisk/sounds/defaults/no_selection.wav");

        if (!$no_selection_copy) {
            return $this->view->render($response, 'templates/forms/settings.twig', [
                'user' => $user,
                'error' => 'No Selection prompt not saved',
                'files' => Files::where('tag', 'prompt')->get(),
                'setting' => $settings
            ]);
        }

        $selection_copy = copy($settings->selection_confirmation_path, "/var/lib/asterisk/sounds/defaults/selection_confirmation.wav");

        if (!$selection_copy) {
            return $this->view->render($response, 'templates/forms/settings.twig', [
                'user' => $user,
                'error' => 'Selection Confirmation prompt not saved',
                'files' => Files::where('tag', 'prompt')->get(),
                'setting' => $settings
            ]);
        }

        $success_path = copy($settings->success_path, "/var/lib/asterisk/sounds/defaults/success.wav");

        if (!$success_path) {
            return $this->view->render($response, 'templates/forms/settings.twig', [
                'user' => $user,
                'error' => 'Successful subscription prompt not saved',
                'files' => Files::where('tag', 'prompt')->get(),
                'setting' => $settings
            ]);
        }

        $failure_copy = copy($settings->subscription_failure_path, "/var/lib/asterisk/sounds/defaults/failure.wav");

        if (!$failure_copy) {
            return $this->view->render($response, 'templates/forms/settings.twig', [
                'user' => $user,
                'error' => 'Subscription Failure prompt not saved',
                'files' => Files::where('tag', 'prompt')->get(),
                'setting' => $settings
            ]);
        }

        $continue_copy = copy($settings->continue_path, "/var/lib/asterisk/sounds/defaults/continue.wav");

        if (!$continue_copy) {
            return $this->view->render($response, 'templates/forms/settings.twig', [
                'user' => $user,
                'error' => 'Continue prompt not saved',
                'files' => Files::where('tag', 'prompt')->get(),
                'setting' => $settings
            ]);
        }

        $goodbye_copy = copy($settings->goodbye_path, "/var/lib/asterisk/sounds/defaults/goodbye.wav");

        if (!$goodbye_copy) {
            return $this->view->render($response, 'templates/forms/settings.twig', [
                'user' => $user,
                'error' => 'Goodbye prompt not saved',
                'files' => Files::where('tag', 'prompt')->get(),
                'setting' => $settings
            ]);
        }

//        $incorrect_command = 'cp ' . $settings->incorrect_path . ' ' . "/var/lib/asterisk/sounds/defaults/incorrect.wav";
//        shell_exec($incorrect_command);

//        $repeat_command = 'cp ' . $settings->repeat_path . ' ' . "/var/lib/asterisk/sounds/defaults/repeat.wav";
//        shell_exec($repeat_command);

//        $con_command = 'cp ' . $settings->confirmation_path . ' ' . "/var/lib/asterisk/sounds/defaults/confirmation.wav";
//        shell_exec($con_command);

//        $settings_command = 'cp ' . $settings->goodbye_path . ' ' . "/var/lib/asterisk/sounds/defaults/goodbye.wav";
//        shell_exec($settings_command);

//        $wrong_command = 'cp ' . $settings->wrong_path . ' ' . "/var/lib/asterisk/sounds/defaults/wrong.wav";
//        shell_exec($wrong_command);

//        $success_command = 'cp ' . $settings->success_path . ' ' . "/var/lib/asterisk/sounds/defaults/success.wav";
//        shell_exec($success_command);

//        $no_selection_command = 'cp ' . $settings->no_selection_path . ' ' . "/var/lib/asterisk/sounds/defaults/no_selection.wav";
//        shell_exec($no_selection_command);

//        $select_command = 'cp ' . $settings->selection_confirmation_path . ' ' . "/var/lib/asterisk/sounds/defaults/selection_confirmation.wav";
//        shell_exec($select_command);

//        $sub_failure_command = 'cp ' . $settings->subscription_failure_path . ' ' . "/var/lib/asterisk/sounds/defaults/failure.wav";
//        shell_exec($sub_failure_command);

//        $continue_command = 'cp ' . $settings->continue_path . ' ' . "/var/lib/asterisk/sounds/defaults/continue.wav";
//        shell_exec($continue_command);

        Index::index('settings', [
                'id' => $settings->id,
                'advert_limit' => $settings->advert_limit,
                'incorrect_path' => $settings->incorrect_path,
                'repeat_path' => $settings->repeat_path,
                'confirmation_path' => $settings->confirmation_path,
                'goodbye_path' => $settings->goodbye_path,
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