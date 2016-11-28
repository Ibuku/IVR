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
use App\Services\Index;
use DateTime;
use Respect\Validation\Validator as Val;
use App\Models\Action;

class CampaignController extends BaseController
{
    public function getPage($request, $response){

        $user = $this->auth->user();

        $campaigns = json_encode(Campaign::all());

        return $this->view->render($response, 'templates/campaigns.twig', [
            'campaigns' => $campaigns,
            'username' => $user->username
        ]);
    }

    public function createCampaign($request, $response){

        $user = $this->auth->user();

        $files = Files::where('username', $user->username)->get();

        $options = [
            array("name" => "Subscribe", "value" => "subscribe")
//            array("name" => "Send Message", "value" => "send_message"),
//            array("name" => "Send Image", "value" => "send_image"),
//            array("name" => "Transfer Call", "value" => "transfer_call"),
//            array("name" => "Play File", "value" => "play_file")
        ];

        return $this->view->render($response, 'templates/forms/campaign.twig', [
            'files' => $files,
            'options' => $options,
            'user' => $user
        ]);
    }

    public function postData($request, $response){

        $user = $this->auth->user();

        $match = ['name' => $request->getParam('file'), 'username' => $user->username];


        $file = Files::where($match)->first();
        
        $match = ['file_path' => $file->file_path, 'username' => $user->username];
        
        $campaign = Campaign::where($match)->first();

        if ($campaign)
        {
            $files = Files::where('username', $user->username)->get();
            $options = [
                array("name" => "Subscribe", "value" => "subscribe")
//                array("name" => "Send Message", "value" => "send_message"),
            ];
            $error =  "A campaign using this audio file already exists";
            return $this->view->render($response, 'templates/forms/campaign.twig', [
                'files' => $files,
                'options' => $options,
                'user' => $user,
                'error' => $error
            ]);
        }

        $start_date = DateTime::createFromFormat('d/m/Y', $request->getParam('start_date'))->format('Y-m-d');
        $end_date = DateTime::createFromFormat('d/m/Y', $request->getParam('end_date'))->format('Y-m-d');

        $validation = $this->validator->validate($request, [
            'file' => Val::notEmpty()->verifyFile(),
        ]);

        if ($validation->failed()) {
            return $response->withRedirect($this->router->pathFor('register'));
        }

        $command = 'cp '. $file->file_path. ' '. "/var/lib/asterisk/sounds/files/" . $user->username . '/'. $file->name;

        shell_exec($command);

        $campaign = Campaign::create([
            'username' => $user->username,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'name' => $request->getParam('name'),
            'file_path' => $file->file_path,
            'description' => $request->getParam('description'),
            'value' => $request->getParam('value'),
            'body' => $request->getParam('body'),
            'play_path' => "/var/lib/asterisk/sounds/files/" . $user->username . '/'. $file->name
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

            Index::index('action', [
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
//        if ($request->getParam('one_body')) {
//            array_push($actions, array('number'=>'1', 'value'=>$request->getParam('one_value'), 'body' => $request->getParam('one_body'),
//                "repeat"=>$request->getParam('one_repeat'), "confirm"=>$request->getParam('one_confirm')));
//        }
//
//        if ($request->getParam('two_body')) {
//            array_push($actions, array('number'=>'2', 'value'=>$request->getParam('two_value'), 'body' => $request->getParam('two_body'),
//                "repeat"=>$request->getParam('two_repeat'), "confirm"=>$request->getParam('two_confirm')));
//        }
//
//        if ($request->getParam('three_body')) {
//            array_push($actions, array('number'=>'3', 'value'=>$request->getParam('three_value'), 'body' => $request->getParam('three_body'),
//                "repeat"=>$request->getParam('star_repeat'), "confirm"=>$request->getParam('star_confirm')));
//        }
//
//        if ($request->getParam('four_body')) {
//            array_push($actions, array('number'=>'4', 'value'=>$request->getParam('four_value'), 'body' => $request->getParam('four_body')));
//        }
//
//        if ($request->getParam('five_body')) {
//            array_push($actions, array('number'=>'5', 'value'=>$request->getParam('five_value'), 'body' => $request->getParam('five_body')));
//        }
//
//        if ($request->getParam('six_body')) {
//            array_push($actions, array('number'=>'6', 'value'=>$request->getParam('six_value'), 'body' => $request->getParam('six_body')));
//        }
//
//        if ($request->getParam('seven_body')) {
//            array_push($actions, array('number'=>'7', 'value'=>$request->getParam('seven_value'), 'body' => $request->getParam('seven_body')));
//        }
//
//        if ($request->getParam('eight_body')) {
//            array_push($actions, array('number'=>'8', 'value'=>$request->getParam('eight_value'), 'body' => $request->getParam('eight_body')));
//        }
//
//        if ($request->getParam('nine_body')) {
//            array_push($actions, array('number'=>'9', 'value'=>$request->getParam('nine_value'), 'body' => $request->getParam('nine_body')));
//        }

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

        return $response->withRedirect($this->router->pathFor('campaigns'));

    }

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

        $end_date = new DateTime($campaign->end_date);
        $end = $end_date->format('d/m/Y');

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
//                array("name" => "Send Message", "value" => "send_message")
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
        $end_date = DateTime::createFromFormat('d/m/Y', $request->getParam('end_date'))->format('Y-m-d');

        $campaign->update([
            'name' => $request->getParam('name'),
            'description' => $request->getParam('description'),
            'start_date' => $start_date,
            'end_date' => $end_date
        ]);

        Index::update('campaign', $campaign->id, [
            'name' => $campaign->name,
            'description' => $campaign->description,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'id' => $campaign->id,
            'play_path' => $campaign->play_path
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

            Index::update('action', $action->id, [
                'number' => $action->number,
                'value' => $action->value,
                'body' => $action->body,
                'request' => $action->request,
                'repeat_param' => $action->repeat_param,
                'confirm' => $action->confirm,
                'parameter' => $action->parameter,
                'campaign_id' => $campaign->id,
                'id' => $action->id,
            ]);
        }

//        if ($request->getParam('*_body')) {
//            array_push($actions, array('number'=>'*', 'value'=>$request->getParam('*_value'), 'body' => $request->getParam('*_body')));
//        }
//
//        if ($request->getParam('1_body')) {
//            array_push($actions, array('number'=>'1', 'value'=>$request->getParam('1_value'), 'body' => $request->getParam('1_body')));
//        }
//
//        if ($request->getParam('2_body')) {
//            array_push($actions, array('number'=>'2', 'value'=>$request->getParam('2_value'), 'body' => $request->getParam('2_body')));
//        }
//
//        if ($request->getParam('3_body')) {
//            array_push($actions, array('number'=>'3', 'value'=>$request->getParam('3_value'), 'body' => $request->getParam('3_body')));
//        }
//
//        if ($request->getParam('4_body')) {
//            array_push($actions, array('number'=>'4', 'value'=>$request->getParam('4_value'), 'body' => $request->getParam('4_body')));
//        }
//
//        if ($request->getParam('5_body')) {
//            array_push($actions, array('number'=>'5', 'value'=>$request->getParam('5_value'), 'body' => $request->getParam('5_body')));
//        }
//
//        if ($request->getParam('6_body')) {
//            array_push($actions, array('number'=>'6', 'value'=>$request->getParam('6_value'), 'body' => $request->getParam('6_body')));
//        }
//
//        if ($request->getParam('7_body')) {
//            array_push($actions, array('number'=>'7', 'value'=>$request->getParam('7_value'), 'body' => $request->getParam('7_body')));
//        }
//
//        if ($request->getParam('8_body')) {
//            array_push($actions, array('number'=>'8', 'value'=>$request->getParam('8_value'), 'body' => $request->getParam('8_body')));
//        }
//
//        if ($request->getParam('9_body')) {
//            array_push($actions, array('number'=>'9', 'value'=>$request->getParam('9_value'), 'body' => $request->getParam('9_body')));
//        }

//        foreach ($actions as $value) {
//
//            $match = ['campaign_id' => $campaign->id, 'number' => $value['number']];
//
//            $action = Action::where($match)->first();
//
//            if ($action) {
//
//                $action->update([
//                    'number' => $value['number'],
//                    'value' => $value['value'],
//                    'body' => $value['body']
//                ]);
//
//                Index::update('action', $action->id, [
//                    'number' => $action->number,
//                    'value' => $action->value,
//                    'body' => $action->body,
//                    'campaign_id' => $campaign->id,
//                    'id' => $action->id,
//                ]);
//            }
//        }
        
        return $response->withRedirect($this->router->pathFor('campaigns'));

    }
}