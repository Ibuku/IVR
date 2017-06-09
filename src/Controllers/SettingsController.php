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
                'default_settings' => true,
                'incorrect_path' => $request->getParam('incorrect_path'),
                'no_selection_path' => $request->getParam('no_selection_path'),
                'no_selection_repeat_path' => $request->getParam('no_selection_repeat_path'),
                'no_selection_confirm_subscription_path' => $request->getParam('no_selection_confirm_subscription_path'),
                'success_path' => $request->getParam('success_path'),
                'goodbye_path' => $request->getParam('goodbye_path'),
                'subscription_path' => $request->getParam('subscription_path'),
                'subscription_confirmation_path' => $request->getParam('subscription_confirmation_path'),
                'already_subscribed_path' => $request->getParam('already_subscribed_path'),
                'subscription_failure_path' => $request->getParam('subscription_failure_path'),
                'continue_path' => $request->getParam('continue_path')
            ]);
        } else {
            $settings = Settings::create([
                'advert_limit' => $request->getParam('advert_limit'),
                'default_settings' => true,
                'incorrect_path' => $request->getParam('incorrect_path'),
                'no_selection_path' => $request->getParam('no_selection_path'),
                'no_selection_repeat_path' => $request->getParam('no_selection_repeat_path'),
                'no_selection_confirm_subscription_path' => $request->getParam('no_selection_confirm_subscription_path'),
                'success_path' => $request->getParam('success_path'),
                'goodbye_path' => $request->getParam('goodbye_path'),
                'subscription_path' => $request->getParam('subscription_path'),
                'subscription_confirmation_path' => $request->getParam('subscription_confirmation_path'),
                'already_subscribed_path' => $request->getParam('already_subscribed_path'),
                'subscription_failure_path' => $request->getParam('subscription_failure_path'),
                'continue_path' => $request->getParam('continue_path')
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

        $no_selection_copy = copy($settings->no_selection_path, "/var/lib/asterisk/sounds/defaults/no_selection.wav");

        if (!$no_selection_copy) {
            return $this->view->render($response, 'templates/forms/settings.twig', [
                'user' => $user,
                'error' => 'No Selection prompt not saved',
                'files' => Files::where('tag', 'prompt')->get(),
                'setting' => $settings
            ]);
        }

        $repeat_copy = copy($settings->no_selection_repeat_path, "/var/lib/asterisk/sounds/defaults/no_selection_repeat.wav");

        if (!$repeat_copy) {
            return $this->view->render($response, 'templates/forms/settings.twig', [
                'user' => $user,
                'error' => 'Repeat prompt not saved',
                'files' => Files::where('tag', 'prompt')->get(),
                'setting' => $settings
            ]);
        }

        $con_copy = copy($settings->no_selection_confirm_subscription_path, "/var/lib/asterisk/sounds/defaults/no_selection_confirm_subscription.wav");

        if (!$con_copy) {
            return $this->view->render($response, 'templates/forms/settings.twig', [
                'user' => $user,
                'error' => 'Confirmation prompt not saved',
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

        $goodbye_copy = copy($settings->goodbye_path, "/var/lib/asterisk/sounds/defaults/goodbye.wav");

        if (!$goodbye_copy) {
            return $this->view->render($response, 'templates/forms/settings.twig', [
                'user' => $user,
                'error' => 'Goodbye prompt not saved',
                'files' => Files::where('tag', 'prompt')->get(),
                'setting' => $settings
            ]);
        }

        $sub_copy = copy($settings->subscription_path, "/var/lib/asterisk/sounds/defaults/subscription.wav");

        if (!$sub_copy) {
            return $this->view->render($response, 'templates/forms/settings.twig', [
                'user' => $user,
                'error' => 'Wrong Selection prompt not saved',
                'files' => Files::where('tag', 'prompt')->get(),
                'setting' => $settings
            ]);
        }

        $sub_confirm_copy = copy($settings->subscription_confirmation_path, "/var/lib/asterisk/sounds/defaults/subscription_confirmation.wav");

        if (!$sub_confirm_copy) {
            return $this->view->render($response, 'templates/forms/settings.twig', [
                'user' => $user,
                'error' => 'Selection Confirmation prompt not saved',
                'files' => Files::where('tag', 'prompt')->get(),
                'setting' => $settings
            ]);
        }

        $already_subscribed_copy = copy($settings->already_subscribed_path, "/var/lib/asterisk/sounds/defaults/already_subscribed.wav");

        if (!$already_subscribed_copy) {
            return $this->view->render($response, 'templates/forms/settings.twig', [
                'user' => $user,
                'error' => 'Continue prompt not saved',
                'files' => Files::where('tag', 'prompt')->get(),
                'setting' => $settings
            ]);
        }

        $failure_copy = copy($settings->subscription_failure_path, "/var/lib/asterisk/sounds/defaults/subscription_failure.wav");

        if (!$failure_copy) {
            return $this->view->render($response, 'templates/forms/settings.twig', [
                'user' => $user,
                'error' => 'Subscription Failure prompt not saved',
                'files' => Files::where('tag', 'prompt')->get(),
                'setting' => $settings
            ]);
        }

        $continue_copy = copy($settings->continue_path, "/var/lib/asterisk/sounds/defaults/continue.wav");
        shell_exec($continue_copy);

        if (!$continue_copy) {
            return $this->view->render($response, 'templates/forms/settings.twig', [
                'user' => $user,
                'error' => 'Continue listening prompt not saved',
                'files' => Files::where('tag', 'prompt')->get(),
                'setting' => $settings
            ]);
        }

        Index::index('settings', [
                'id' => $settings->id,
                'advert_limit' => $settings->advert_limit,
                'default_settings' => true,
                'incorrect_path' => "/var/lib/asterisk/sounds/defaults/incorrect.wav",
                'no_selection_path' => "/var/lib/asterisk/sounds/defaults/no_selection.wav",
                'no_selection_repeat_path' => "/var/lib/asterisk/sounds/defaults/no_selection_repeat.wav",
                'no_selection_confirm_subscription_path' => "/var/lib/asterisk/sounds/defaults/no_selection_confirm_subscription.wav",
                'success_path' => "/var/lib/asterisk/sounds/defaults/success.wav",
                'goodbye_path' => "/var/lib/asterisk/sounds/defaults/goodbye.wav",
                'subscription_path' => "/var/lib/asterisk/sounds/defaults/subscription.wav",
                'subscription_confirmation_path' => "/var/lib/asterisk/sounds/defaults/subscription_confirmation.wav",
                'already_subscribed_path' => "/var/lib/asterisk/sounds/defaults/already_subscribed.wav",
                'subscription_failure_path' => "/var/lib/asterisk/sounds/defaults/subscription_failure.wav",
                'continue_path' => "/var/lib/asterisk/sounds/defaults/continue.wav"
            ]
        );

        return $response->withRedirect($this->router->pathFor('settings'));

    }
}