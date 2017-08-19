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
                'repeat_path' => $request->getParam('repeat_path'),
                'success_path' => $request->getParam('success_path'),
                'goodbye_path' => $request->getParam('goodbye_path'),
                'subscription_path' => $request->getParam('subscription_path'),
                'subscription_confirmation_path' => $request->getParam('subscription_confirmation_path'),
                'already_subscribed_path' => $request->getParam('already_subscribed_path'),
                'insufficient_balance_path' => $request->getParam('insufficient_balance_path'),
                'subscription_failure_path' => $request->getParam('subscription_failure_path'),
                'continue_path' => $request->getParam('continue_path'),
                'wrong_selection_path' => $request->getParam('wrong_selection_path')
            ]);
        } else {
            $settings = Settings::create([
                'advert_limit' => $request->getParam('advert_limit'),
                'default_settings' => true,
                'incorrect_path' => $request->getParam('incorrect_path'),
                'no_selection_path' => $request->getParam('no_selection_path'),
                'repeat_path' => $request->getParam('repeat_path'),
                'success_path' => $request->getParam('success_path'),
                'goodbye_path' => $request->getParam('goodbye_path'),
                'subscription_path' => $request->getParam('subscription_path'),
                'subscription_confirmation_path' => $request->getParam('subscription_confirmation_path'),
                'already_subscribed_path' => $request->getParam('already_subscribed_path'),
                'insufficient_balance_path' => $request->getParam('insufficient_balance_path'),
                'subscription_failure_path' => $request->getParam('subscription_failure_path'),
                'continue_path' => $request->getParam('continue_path'),
                'wrong_selection_path' => $request->getParam('wrong_selection_path')
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

        $repeat_copy = copy($settings->repeat_path, "/var/lib/asterisk/sounds/defaults/repeat.wav");

        if (!$repeat_copy) {
            return $this->view->render($response, 'templates/forms/settings.twig', [
                'user' => $user,
                'error' => 'Repeat prompt not saved',
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
                'error' => 'Already Subscribed prompt not saved',
                'files' => Files::where('tag', 'prompt')->get(),
                'setting' => $settings
            ]);
        }

        $insufficient_copy = copy($settings->insufficient_balance_path, "/var/lib/asterisk/sounds/defaults/insufficient.wav");

        if (!$insufficient_copy) {
            return $this->view->render($response, 'templates/forms/settings.twig', [
                'user' => $user,
                'error' => 'Insufficient prompt not saved',
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

        $wrong_copy = copy($settings->wrong_selection_path, "/var/lib/asterisk/sounds/defaults/wrong.wav");
        shell_exec($wrong_copy);

        if (!$wrong_copy) {
            return $this->view->render($response, 'templates/forms/settings.twig', [
                'user' => $user,
                'error' => 'Wrong Selection prompt not saved',
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
                'repeat_path' => "/var/lib/asterisk/sounds/defaults/repeat.wav",
                'success_path' => "/var/lib/asterisk/sounds/defaults/success.wav",
                'goodbye_path' => "/var/lib/asterisk/sounds/defaults/goodbye.wav",
                'subscription_path' => "/var/lib/asterisk/sounds/defaults/subscription.wav",
                'subscription_confirmation_path' => "/var/lib/asterisk/sounds/defaults/subscription_confirmation.wav",
                'already_subscribed_path' => "/var/lib/asterisk/sounds/defaults/already_subscribed.wav",
                'insufficient_balance_path' => '/var/lib/asterisk/sounds/defaults/insufficient.wav',
                'subscription_failure_path' => "/var/lib/asterisk/sounds/defaults/subscription_failure.wav",
                'continue_path' => "/var/lib/asterisk/sounds/defaults/continue.wav",
                "wrong_selection_path" => "/var/lib/asterisk/sounds/defaults/wrong.wav",
            ]
        );

        return $response->withRedirect($this->router->pathFor('settings'));

    }

//    public function postData($request, $response)
//    {
//
//        $settings = Settings::first();
//        $user = $this->auth->user();
//
//        if ($settings) {
//            $settings->update([
//                'advert_limit' => $request->getParam('advert_limit'),
//                'default_settings' => true,
//                'incorrect_path' => $request->getParam('incorrect_path'),
//                'no_selection_path' => $request->getParam('no_selection_path'),
//                'repeat_path' => $request->getParam('repeat_path'),
////                'no_selection_confirm_subscription_path' => $request->getParam('no_selection_confirm_subscription_path'),
//                'success_path' => $request->getParam('success_path'),
//                'goodbye_path' => $request->getParam('goodbye_path'),
//                'subscription_path' => $request->getParam('subscription_path'),
//                'subscription_confirmation_path' => $request->getParam('subscription_confirmation_path'),
//                'already_subscribed_path' => $request->getParam('already_subscribed_path'),
//                'subscription_failure_path' => $request->getParam('subscription_failure_path'),
//                'continue_path' => $request->getParam('continue_path'),
//                'wrong_selection_path' => $request->getParam('wrong_selection_path')
//            ]);
//        } else {
//            $settings = Settings::create([
//                'advert_limit' => $request->getParam('advert_limit'),
//                'default_settings' => true,
//                'incorrect_path' => $request->getParam('incorrect_path'),
//                'no_selection_path' => $request->getParam('no_selection_path'),
//                'repeat_path' => $request->getParam('repeat_path'),
////                'no_selection_confirm_subscription_path' => $request->getParam('no_selection_confirm_subscription_path'),
//                'success_path' => $request->getParam('success_path'),
//                'goodbye_path' => $request->getParam('goodbye_path'),
//                'subscription_path' => $request->getParam('subscription_path'),
//                'subscription_confirmation_path' => $request->getParam('subscription_confirmation_path'),
//                'already_subscribed_path' => $request->getParam('already_subscribed_path'),
//                'subscription_failure_path' => $request->getParam('subscription_failure_path'),
//                'continue_path' => $request->getParam('continue_path'),
//                'wrong_selection_path' => $request->getParam('wrong_selection_path')
//            ]);
//        }
//
//        $incorrect_copy = static::send_via_remote($this->settings['REMOTE']['URL'], $this->settings['REMOTE']['USERNAME'],
//            $this->settings['REMOTE']['PASSWORD'], $settings->incorrect_path,
//            "/var/lib/asterisk/sounds/defaults/incorrect.wav");
//
//        if (!$incorrect_copy) {
//            return $this->view->render($response, 'templates/forms/settings.twig', [
//                'user' => $user,
//                'error' => 'Incorrect prompt not saved',
//                'files' => Files::where('tag', 'prompt')->get(),
//                'setting' => $settings
//            ]);
//        }
//
//        $no_selection_copy = static::send_via_remote($this->settings['REMOTE']['URL'], $this->settings['REMOTE']['USERNAME'],
//            $this->settings['REMOTE']['PASSWORD'], $settings->no_selection_path,
//            "/var/lib/asterisk/sounds/defaults/no_selection.wav");
//
//        if (!$no_selection_copy) {
//            return $this->view->render($response, 'templates/forms/settings.twig', [
//                'user' => $user,
//                'error' => 'No Selection prompt not saved',
//                'files' => Files::where('tag', 'prompt')->get(),
//                'setting' => $settings
//            ]);
//        }
//
//        $repeat_copy = static::send_via_remote($this->settings['REMOTE']['URL'], $this->settings['REMOTE']['USERNAME'],
//            $this->settings['REMOTE']['PASSWORD'], $settings->repeat_path,
//            "/var/lib/asterisk/sounds/defaults/repeat.wav");
//
//        if (!$repeat_copy) {
//            return $this->view->render($response, 'templates/forms/settings.twig', [
//                'user' => $user,
//                'error' => 'Repeat prompt not saved',
//                'files' => Files::where('tag', 'prompt')->get(),
//                'setting' => $settings
//            ]);
//        }
//
////        $con_copy = static::send_via_remote($this->settings['REMOTE']['URL'], $this->settings['REMOTE']['USERNAME'],
////            $this->settings['REMOTE']['PASSWORD'], $settings->no_selection_confirm_subscription_path,
////            "/var/lib/asterisk/sounds/defaults/no_selection_confirm_subscription.wav");
////
////        if (!$con_copy) {
////            return $this->view->render($response, 'templates/forms/settings.twig', [
////                'user' => $user,
////                'error' => 'Confirmation prompt not saved',
////                'files' => Files::where('tag', 'prompt')->get(),
////                'setting' => $settings
////            ]);
////        }
//
//        $success_path = static::send_via_remote($this->settings['REMOTE']['URL'], $this->settings['REMOTE']['USERNAME'],
//            $this->settings['REMOTE']['PASSWORD'], $settings->success_path,  "/var/lib/asterisk/sounds/defaults/success.wav");
//
//        if (!$success_path) {
//            return $this->view->render($response, 'templates/forms/settings.twig', [
//                'user' => $user,
//                'error' => 'Successful subscription prompt not saved',
//                'files' => Files::where('tag', 'prompt')->get(),
//                'setting' => $settings
//            ]);
//        }
//
//        $goodbye_copy = static::send_via_remote($this->settings['REMOTE']['URL'], $this->settings['REMOTE']['USERNAME'],
//            $this->settings['REMOTE']['PASSWORD'], $settings->goodbye_path,  "/var/lib/asterisk/sounds/defaults/goodbye.wav");
//
//        if (!$goodbye_copy) {
//            return $this->view->render($response, 'templates/forms/settings.twig', [
//                'user' => $user,
//                'error' => 'Goodbye prompt not saved',
//                'files' => Files::where('tag', 'prompt')->get(),
//                'setting' => $settings
//            ]);
//        }
//
//        $sub_copy = static::send_via_remote($this->settings['REMOTE']['URL'], $this->settings['REMOTE']['USERNAME'],
//            $this->settings['REMOTE']['PASSWORD'], $settings->subscription_path,  "/var/lib/asterisk/sounds/defaults/subscription.wav");
//
//        if (!$sub_copy) {
//            return $this->view->render($response, 'templates/forms/settings.twig', [
//                'user' => $user,
//                'error' => 'Wrong Selection prompt not saved',
//                'files' => Files::where('tag', 'prompt')->get(),
//                'setting' => $settings
//            ]);
//        }
//
//        $sub_confirm_copy = static::send_via_remote($this->settings['REMOTE']['URL'], $this->settings['REMOTE']['USERNAME'],
//            $this->settings['REMOTE']['PASSWORD'], $settings->subscription_confirmation_path,  "/var/lib/asterisk/sounds/defaults/subscription_confirmation.wav");
//
//        if (!$sub_confirm_copy) {
//            return $this->view->render($response, 'templates/forms/settings.twig', [
//                'user' => $user,
//                'error' => 'Selection Confirmation prompt not saved',
//                'files' => Files::where('tag', 'prompt')->get(),
//                'setting' => $settings
//            ]);
//        }
//
//        $already_subscribed_copy = static::send_via_remote($this->settings['REMOTE']['URL'], $this->settings['REMOTE']['USERNAME'],
//            $this->settings['REMOTE']['PASSWORD'], $settings->already_subscribed_path,  "/var/lib/asterisk/sounds/defaults/already_subscribed.wav");
//
//        if (!$already_subscribed_copy) {
//            return $this->view->render($response, 'templates/forms/settings.twig', [
//                'user' => $user,
//                'error' => 'Continue prompt not saved',
//                'files' => Files::where('tag', 'prompt')->get(),
//                'setting' => $settings
//            ]);
//        }
//
//        $failure_copy = static::send_via_remote($this->settings['REMOTE']['URL'], $this->settings['REMOTE']['USERNAME'],
//            $this->settings['REMOTE']['PASSWORD'], $settings->subscription_failure_path,  "/var/lib/asterisk/sounds/defaults/subscription_failure.wav");
//
//        if (!$failure_copy) {
//            return $this->view->render($response, 'templates/forms/settings.twig', [
//                'user' => $user,
//                'error' => 'Subscription Failure prompt not saved',
//                'files' => Files::where('tag', 'prompt')->get(),
//                'setting' => $settings
//            ]);
//        }
//
//        $continue_copy = static::send_via_remote($this->settings['REMOTE']['URL'], $this->settings['REMOTE']['USERNAME'],
//            $this->settings['REMOTE']['PASSWORD'], $settings->continue_path,  "/var/lib/asterisk/sounds/defaults/continue.wav");
//
//        if (!$continue_copy) {
//            return $this->view->render($response, 'templates/forms/settings.twig', [
//                'user' => $user,
//                'error' => 'Continue listening prompt not saved',
//                'files' => Files::where('tag', 'prompt')->get(),
//                'setting' => $settings
//            ]);
//        }
//
//        $wrong_copy = static::send_via_remote($this->settings['REMOTE']['URL'], $this->settings['REMOTE']['USERNAME'],
//            $this->settings['REMOTE']['PASSWORD'], $settings->wrong_selection_path,  "/var/lib/asterisk/sounds/defaults/wrong.wav");
//
//        if (!$wrong_copy) {
//            return $this->view->render($response, 'templates/forms/settings.twig', [
//                'user' => $user,
//                'error' => 'Wrong Selection prompt not saved',
//                'files' => Files::where('tag', 'prompt')->get(),
//                'setting' => $settings
//            ]);
//        }
//
//        Index::index('settings', [
//                'id' => $settings->id,
//                'advert_limit' => $settings->advert_limit,
//                'default_settings' => true,
//                'incorrect_path' => "/var/lib/asterisk/sounds/defaults/incorrect.wav",
//                'no_selection_path' => "/var/lib/asterisk/sounds/defaults/no_selection.wav",
//                'repeat_path' => "/var/lib/asterisk/sounds/defaults/repeat.wav",
////                'no_selection_confirm_subscription_path' => "/var/lib/asterisk/sounds/defaults/no_selection_confirm_subscription.wav",
//                'success_path' => "/var/lib/asterisk/sounds/defaults/success.wav",
//                'goodbye_path' => "/var/lib/asterisk/sounds/defaults/goodbye.wav",
//                'subscription_path' => "/var/lib/asterisk/sounds/defaults/subscription.wav",
//                'subscription_confirmation_path' => "/var/lib/asterisk/sounds/defaults/subscription_confirmation.wav",
//                'already_subscribed_path' => "/var/lib/asterisk/sounds/defaults/already_subscribed.wav",
//                'subscription_failure_path' => "/var/lib/asterisk/sounds/defaults/subscription_failure.wav",
//                'continue_path' => "/var/lib/asterisk/sounds/defaults/continue.wav",
//                "wrong_selection_path" => "/var/lib/asterisk/sounds/defaults/wrong.wav",
//            ]
//        );
//
//        return $response->withRedirect($this->router->pathFor('settings'));
//
//    }
}