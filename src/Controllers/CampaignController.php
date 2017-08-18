<?php
/**
 * Created by PhpStorm.
 * User: stikks
 * Date: 9/29/16
 * Time: 11:48 AM
 */

namespace App\Controllers;
use App\Models\Campaign;
use App\Models\Files;
use App\Models\User;
use App\Services\Index;
use DateTime;
use Illuminate\Support\Facades\File;
use Respect\Validation\Validator as Val;
use App\Models\Action;

class CampaignController extends BaseController
{
    public function getPage($request, $response){

        $user = $this->auth->user();

        $campaigns = json_encode(Campaign::all());

        return $this->view->render($response, 'templates/campaigns.twig', [
            'user' => $user,
            'campaigns' => $campaigns,
            'users' => User::all(),
            'username' => $user->username
        ]);
    }

    public function createCampaign($request, $response){

        $user = $this->auth->user();

        $files = Files::where('tag', 'advert')->get();

        if ($user->username != $this->settings['DEFAULT_ACCOUNT']) {
            $match = ['tag'=>'advert', 'username'=>$user->username];
            $files = Files::where($match)->get();
        }

        $users = User::all();

        $options = [
            array("name" => "Subscribe", "value" => "subscribe")
        ];

        $default = $user->username == $this->settings['DEFAULT_ACCOUNT'] ? true: false;

        return $this->view->render($response, 'templates/forms/campaign.twig', [
            'files' => $files,
            'options' => $options,
            'user' => $user,
            'users' => $users,
            'default' => $default
        ]);
    }

    public function postData($request, $response){

        $user = $this->auth->user();

        $username = $user->username;

        if ($request->getParam('username')) {
            $username = $request->getParam('username');
        }

        $file_match = ['name' => $request->getParam('file')];

        $file = Files::where($file_match)->first();

        $match = ['file_path' => $file->file_path, 'username' => $username];

        $campaign = Campaign::where($match)->first();

        if ($campaign)
        {
            $files = Files::where('tag', 'advert')->get();

            if ($user->username != $this->settings['DEFAULT_ACCOUNT']) {
                $match = ['tag'=>'advert', 'username'=>$user->username];
                $files = Files::where($match)->get();
            }

            $options = [
                array("name" => "Subscribe", "value" => "subscribe")
            ];

            $users = User::all();

            $error =  "A campaign using this audio file already exists";

            return $this->view->render($response, 'templates/forms/campaign.twig', [
                'files' => $files,
                'options' => $options,
                'user' => $user,
                'error' => $error,
                'users' => $users
            ]);
        }

        $start_date = date('Y-m-d');

        if ($request->getParam('start_date')) {
            $start_date = DateTime::createFromFormat('d/m/Y', $request->getParam('start_date'))->format('Y-m-d');
        }

        $end_date = null;

        if ($request->getParam('end_date')) {
            $end_date = DateTime::createFromFormat('d/m/Y', $request->getParam('end_date'))->format('Y-m-d');
        }

        $validation = $this->validator->validate($request, [
            'file' => Val::notEmpty()->verifyFile(),
        ]);

        if ($validation->failed()) {
            return $response->withRedirect($this->router->pathFor('create_campaign'));
        }

        $file_split = explode('/', $file->file_path);
        $file_name = end($file_split);

//        $command = 'cp '. $file->file_path. ' '. "/var/lib/asterisk/sounds/files/inactive/" . $username . '/'. $file_name;
//        shell_exec($command);

        $play_path = "/var/lib/asterisk/sounds/files/{$username}/{$file_name}";
        $file_copy = copy($file->file_path, "/var/lib/asterisk/sounds/files/inactive/{$username}/{$file_name}");

        if (!$file_copy) {
            $files = Files::where('tag', 'advert')->get();

            if ($user->username != $this->settings['DEFAULT_ACCOUNT']) {
                $match = ['tag'=>'advert', 'username'=>$user->username];
                $files = Files::where($match)->get();
            }

            $options = [
                array("name" => "Subscribe", "value" => "subscribe")
            ];

            $users = User::all();

            $error =  "Campaign audio file not moved.";

            return $this->view->render($response, 'templates/forms/campaign.twig', [
                'files' => $files,
                'options' => $options,
                'user' => $user,
                'error' => $error,
                'users' => $users
            ]);
        }

        $campaign = Campaign::create([
            'username' => $username,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'name' => $request->getParam('name'),
            'file_path' => $file->file_path,
            'description' => $request->getParam('description'),
            'value' => $request->getParam('value'),
            'body' => $request->getParam('body'),
            'is_active' => false,
            'play_path' => $play_path
        ]);

        if ($request->getParam('body') && $request->getParam('number') && $request->getParam('value')) {
            $value = array('number'=>$request->getParam('number'), 'value'=>$request->getParam('value'), 'parameter' => $request->getParam('parameter'),
                'body' => $request->getParam('body'), "repeat_param"=>$request->getParam('repeat_param'),
                "confirm"=>$request->getParam('confirm'), "request"=>$request->getParam('request'));

            $action = Action::create([
                'number' => $value['number'],
                'value' => $value['value'],
                'body' => $value['body'],
                'repeat_param' => $value['repeat_param'],
                'confirm' => $value['confirm'],
                'request' => $value['request'],
                'parameter' => $value['parameter'],
                'campaign_id' => $campaign->id
            ]);

            Index::save_redis($campaign->play_path. ':'. $action->number, [
                'number' => $action->number,
                'value' => $action->value,
                'body' => $action->body,
                'repeat_param' => $action->repeat_param,
                'confirm' => $action->confirm,
                'parameter' => $action->parameter,
                'request' => $action->request,
                'campaign_id' => $campaign->id,
                'id' => $action->id,
            ]);
        }

        Index::index('campaign', [
            'username' => $campaign->username,
            'start_date' => $campaign->start_date,
            'end_date' => $campaign->end_date,
            'name' => $campaign->name,
            'file_path' => $campaign->file_path,
            'play_path' => $campaign->play_path,
            'description' => $campaign->description,
            'id' => $campaign->id,
            'created_at' => $campaign->created_at->format('Y-m-d'),
            'updated_at' => $campaign->updated_at->format('Y-m-d'),
            'is_active' => $campaign->is_active
        ]);

        Index::save_redis($campaign->play_path, [
            'username' => $campaign->username,
            'start_date' => $campaign->start_date,
            'end_date' => $campaign->end_date,
            'name' => $campaign->name,
            'file_path' => $campaign->file_path,
            'play_path' => $campaign->play_path,
            'description' => $campaign->description,
            'id' => $campaign->id,
            'created_at' => $campaign->created_at->format('Y-m-d'),
            'updated_at' => $campaign->updated_at->format('Y-m-d'),
            'is_active' => $campaign->is_active
        ]);

        return $response->withRedirect($this->router->pathFor('campaigns'));

    }

//    public function postData($request, $response){
//
//        $user = $this->auth->user();
//
//        $username = $user->username;
//
//        if ($request->getParam('username')) {
//            $username = $request->getParam('username');
//        }
//
//        $file_match = ['name' => $request->getParam('file')];
//
//        $file = Files::where($file_match)->first();
//
//        $match = ['file_path' => $file->file_path, 'username' => $username];
//
//        $campaign = Campaign::where($match)->first();
//
//        if ($campaign)
//        {
//            $files = Files::where('tag', 'advert')->get();
//
//            if ($user->username != $this->settings['DEFAULT_ACCOUNT']) {
//                $match = ['tag'=>'advert', 'username'=>$user->username];
//                $files = Files::where($match)->get();
//            }
//
//            $options = [
//                array("name" => "Subscribe", "value" => "subscribe")
//            ];
//
//            $users = User::all();
//
//            $error =  "A campaign using this audio file already exists";
//
//            return $this->view->render($response, 'templates/forms/campaign.twig', [
//                'files' => $files,
//                'options' => $options,
//                'user' => $user,
//                'error' => $error,
//                'users' => $users
//            ]);
//        }
//
//        $start_date = date('Y-m-d');
//
//        if ($request->getParam('start_date')) {
//            $start_date = DateTime::createFromFormat('d/m/Y', $request->getParam('start_date'))->format('Y-m-d');
//        }
//
//        $end_date = null;
//
//        if ($request->getParam('end_date')) {
//            $end_date = DateTime::createFromFormat('d/m/Y', $request->getParam('end_date'))->format('Y-m-d');
//        }
//
//        $validation = $this->validator->validate($request, [
//            'file' => Val::notEmpty()->verifyFile(),
//        ]);
//
//        if ($validation->failed()) {
//            return $response->withRedirect($this->router->pathFor('create_campaign'));
//        }
//
//        $file_split = explode('/', $file->file_path);
//        $file_name = end($file_split);
//
//        $play_path = "/var/lib/asterisk/sounds/files/{$username}/{$file_name}";
//        $file_copy = static::send_via_remote($this->settings['REMOTE']['URL'], $this->settings['REMOTE']['USERNAME'],
//            $this->settings['REMOTE']['PASSWORD'], $file->file_path, "/var/lib/asterisk/sounds/files/inactive/{$username}/{$file_name}");
////        $file_copy = copy($file->file_path, "/var/lib/asterisk/sounds/files/inactive/{$username}/{$file_name}");
//
//        if (!$file_copy) {
//            $files = Files::where('tag', 'advert')->get();
//
//            if ($user->username != $this->settings['DEFAULT_ACCOUNT']) {
//                $match = ['tag'=>'advert', 'username'=>$user->username];
//                $files = Files::where($match)->get();
//            }
//
//            $options = [
//                array("name" => "Subscribe", "value" => "subscribe")
//            ];
//
//            $users = User::all();
//
//            $error =  "Campaign audio file not moved.";
//
//            return $this->view->render($response, 'templates/forms/campaign.twig', [
//                'files' => $files,
//                'options' => $options,
//                'user' => $user,
//                'error' => $error,
//                'users' => $users
//            ]);
//        }
//
//        $campaign = Campaign::create([
//            'username' => $username,
//            'start_date' => $start_date,
//            'end_date' => $end_date,
//            'name' => $request->getParam('name'),
//            'file_path' => $file->file_path,
//            'description' => $request->getParam('description'),
//            'value' => $request->getParam('value'),
//            'body' => $request->getParam('body'),
//            'is_active' => false,
//            'play_path' => $play_path
//        ]);
//
//        if ($request->getParam('body') && $request->getParam('number') && $request->getParam('value')) {
//            $value = array('number'=>$request->getParam('number'), 'value'=>$request->getParam('value'), 'parameter' => $request->getParam('parameter'),
//                'body' => $request->getParam('body'), "repeat_param"=>$request->getParam('repeat_param'),
//                "confirm"=>$request->getParam('confirm'), "request"=>$request->getParam('request'));
//
//            $action = Action::create([
//                'number' => $value['number'],
//                'value' => $value['value'],
//                'body' => $value['body'],
//                'repeat_param' => $value['repeat_param'],
//                'confirm' => $value['confirm'],
//                'request' => $value['request'],
//                'parameter' => $value['parameter'],
//                'campaign_id' => $campaign->id
//            ]);
//
//            Index::save_redis($campaign->play_path. ':'. $action->number, [
//                'number' => $action->number,
//                'value' => $action->value,
//                'body' => $action->body,
//                'repeat_param' => $action->repeat_param,
//                'confirm' => $action->confirm,
//                'parameter' => $action->parameter,
//                'request' => $action->request,
//                'campaign_id' => $campaign->id,
//                'id' => $action->id,
//            ]);
//        }
//
//        Index::index('campaign', [
//            'username' => $campaign->username,
//            'start_date' => $campaign->start_date,
//            'end_date' => $campaign->end_date,
//            'name' => $campaign->name,
//            'file_path' => $campaign->file_path,
//            'play_path' => $campaign->play_path,
//            'description' => $campaign->description,
//            'id' => $campaign->id,
//            'created_at' => $campaign->created_at->format('Y-m-d'),
//            'updated_at' => $campaign->updated_at->format('Y-m-d'),
//            'is_active' => $campaign->is_active
//        ]);
//
//        Index::save_redis($campaign->play_path, [
//            'username' => $campaign->username,
//            'start_date' => $campaign->start_date,
//            'end_date' => $campaign->end_date,
//            'name' => $campaign->name,
//            'file_path' => $campaign->file_path,
//            'play_path' => $campaign->play_path,
//            'description' => $campaign->description,
//            'id' => $campaign->id,
//            'created_at' => $campaign->created_at->format('Y-m-d'),
//            'updated_at' => $campaign->updated_at->format('Y-m-d'),
//            'is_active' => $campaign->is_active
//        ]);
//
//        return $response->withRedirect($this->router->pathFor('campaigns'));
//
//    }

    public function updateCampaign($request, $response, $args){

        $user = $this->auth->user();

        if (!isset($args['campaign_id'])) {
            return $response->withRedirect($this->router->pathFor('campaigns'));
        }

        $campaign_id = $args['campaign_id'];

        $campaign = Campaign::where('id', $campaign_id)->first();

        $files = Files::where('file_path', $campaign->file_path)->get();

        $start_date = new DateTime($campaign->start_date);
        $start = $start_date->format('d/m/Y');

        $end = null;

        if ($campaign->end_date) {
            $end_date = new DateTime($campaign->end_date);
            $end = $end_date->format('d/m/Y');
        }

        $action = Action::where('campaign_id', $campaign->id)->first();

        return $this->view->render($response, 'templates/forms/update_campaign.twig', [
            'campaign' => $campaign,
            'user' => $user,
            'files' => $files,
            'start' => $start,
            'end' => $end,
            'action' => $action,
            'options' => [
                array("name" => "Subscribe", "value" => "subscribe")
            ]
        ]);
    }

    public function postUpdate($request, $response, $args){

        if (!isset($args['campaign_id'])) {
            return $response->withRedirect($this->router->pathFor('campaigns'));
        }

        $campaign_id = $args['campaign_id'];

        $campaign = Campaign::where('id', $campaign_id)->first();

        $start_date = DateTime::createFromFormat('d/m/Y', $request->getParam('start_date'))->format('Y-m-d');

        $end_date = null;

        if ($request->getParam('end_date')) {
            $end_date = DateTime::createFromFormat('d/m/Y', $request->getParam('end_date'))->format('Y-m-d');
        }

        $campaign->update([
            'name' => $request->getParam('name'),
            'description' => $request->getParam('description'),
            'start_date' => $start_date,
            'end_date' => $end_date
        ]);

//        Index::update('campaign', $campaign->id, [
//            'name' => $campaign->name,
//            'description' => $campaign->description,
//            'start_date' => $start_date,
//            'end_date' => $end_date,
//            'id' => $campaign->id,
//            'play_path' => $campaign->play_path
//        ]);

        Index::save_redis($campaign->play_path, [
            'username' => $campaign->username,
            'start_date' => $campaign->start_date,
            'end_date' => $campaign->end_date,
            'name' => $campaign->name,
            'file_path' => $campaign->file_path,
            'play_path' => $campaign->play_path,
            'description' => $campaign->description,
            'id' => $campaign->id,
            'created_at' => $campaign->created_at->format('Y-m-d'),
            'updated_at' => $campaign->updated_at->format('Y-m-d'),
            'is_active' => $campaign->is_active
        ]);

        $action = Action::where('campaign_id', $campaign->id)->first();

        if ($action) {

            $action->update([
                'number' => $request->getParam('number'),
                'value' => $request->getParam('value'),
                'body' => $request->getParam('body'),
                'request' => $request->getParam('request'),
                'parameter' => $request->getParam('parameter'),
                'repeat_param' => $request->getParam('repeat_param'),
                'confirm' => $request->getParam('confirm')
            ]);

//            Index::update('action', $action->id, [
//                'number' => $action->number,
//                'value' => $action->value,
//                'body' => $action->body,
//                'request' => $action->request,
//                'repeat_param' => $action->repeat_param,
//                'confirm' => $action->confirm,
//                'parameter' => $action->parameter,
//                'campaign_id' => $campaign->id,
//                'id' => $action->id,
//            ]);
            Index::save_redis($campaign->play_path. ':'. $action->number, [
                'number' => $action->number,
                'value' => $action->value,
                'body' => $action->body,
                'repeat_param' => $action->repeat_param,
                'confirm' => $action->confirm,
                'parameter' => $action->parameter,
                'request' => $action->request,
                'campaign_id' => $campaign->id,
                'id' => $action->id,
            ]);
        }

        else {
            $action = Action::create([
                'number' => $request->getParam('number'),
                'value' => $request->getParam('value'),
                'body' => $request->getParam('body'),
                'request' => $request->getParam('request'),
                'parameter' => $request->getParam('parameter'),
                'repeat_param' => $request->getParam('repeat_param'),
                'confirm' => $request->getParam('confirm'),
                'campaign_id' => $campaign->id
            ]);

            Index::save_redis($campaign->play_path. ':'. $action->number, [
                'number' => $action->number,
                'value' => $action->value,
                'body' => $action->body,
                'repeat_param' => $action->repeat_param,
                'confirm' => $action->confirm,
                'parameter' => $action->parameter,
                'request' => $action->request,
                'campaign_id' => $campaign->id,
                'id' => $action->id,
            ]);
        }
        
        return $response->withRedirect($this->router->pathFor('campaigns'));

    }

    public function deactivateCampaign($request, $response, $args) {

        if (!isset($args['campaign_id'])) {
            return $response->withStatus(404);
        };

        $user = $this->auth->user();
        $match = ['username' => $user->username, 'id' => $args['campaign_id']];

        if ($user->username == $this->settings['DEFAULT_ACCOUNT']) {
            $match = ['id' => $args['campaign_id']];
        }

        $campaign = Campaign::where($match)->first();

        if (!$campaign) {
            return $response->withStatus(404);
        };

        $file_split = explode('/', $campaign->play_path);
        $file_name = end($file_split);

        try {
//            $transfer = static::rename_remotely($this->settings['REMOTE']['URL'], $this->settings['REMOTE']['USERNAME'],
//                $this->settings['REMOTE']['PASSWORD'], $campaign->play_path,
//                '/var/lib/asterisk/sounds/files/inactive/'. $campaign->username. '/'. $file_name);
//            if (!$transfer) {
//                return $response->withStatus(400);
//            }
            rename($campaign->play_path, '/var/lib/asterisk/sounds/files/inactive/'. $campaign->username. '/'. $file_name);
            $campaign->update([
                'is_active' => false,
                'end_date' => date("Y-m-d")
            ]);
        }
        catch (\Exception $e) {
            return $response->withStatus(400);
        }

        return $response->withStatus(200);
    }

    public function activateCampaign($request, $response, $args) {

        if (!isset($args['campaign_id'])) {
            return $response->withStatus(404);
        };

        $user = $this->auth->user();
        $match = ['username' => $user->username, 'id' => $args['campaign_id']];

        if ($user->username == $this->settings['DEFAULT_ACCOUNT']) {
            $match = ['id' => $args['campaign_id']];
        }

        $campaign = Campaign::where($match)->first();

        if (!$campaign) {
            return $response->withStatus(404);
        };

        $end_date = $campaign->end_date;
        if (date("Y-m-d") > $end_date) {
            $end_date = null;
        }

        $file_split = explode('/', $campaign->play_path);
        $file_name = end($file_split);

        try {
//            $transfer = static::rename_remotely($this->settings['REMOTE']['URL'], $this->settings['REMOTE']['USERNAME'],
//                $this->settings['REMOTE']['PASSWORD'],
//                '/var/lib/asterisk/sounds/files/inactive/'. $campaign->username. '/'. $file_name, $campaign->play_path);
//            if (!$transfer) {
//                return $response->withStatus(400);
//            }
            rename('/var/lib/asterisk/sounds/files/inactive/'. $campaign->username. '/'. $file_name, $campaign->play_path);
            $campaign->update([
                'is_active' => true,
                'start_date' => date("Y-m-d"),
                'end_date' => $end_date
            ]);
        }
        catch (\Exception $e) {
            return $response->withStatus(400);
        }

        return $response->withStatus(200);
    }
}