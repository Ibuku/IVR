<?php
/**
 * Created by PhpStorm.
 * User: stikks
 * Date: 9/28/16
 * Time: 1:53 AM
 */

use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$app->group('', function (){
    
    $this->get('/login', 'LoginController:getPage')->setName('login');
    
    $this->post('/login', 'LoginController:postData');

    $this->get('/forgot', 'LoginController:forgotPassword')->setName('forgot');

    $this->post('/forgot', 'LoginController:postForgot');

})->add(new GuestMiddleware($container));
    
$app->group('', function (){

    $this->get('/', 'IndexController:index')->setName('index');

    $this->get('/accounts', 'AccountController:getPage')->setName('accounts');
    $this->get('/accounts/create', 'AccountController:createAccount')->setName('create_account');
    $this->post('/accounts/create', 'AccountController:postData')->setName('post_account');
    $this->post('/accounts/{account_id}/activate', 'AccountController:Activate');
    $this->post('/accounts/{account_id}/deactivate', 'AccountController:Deactivate');

    $this->get('/campaigns', 'CampaignController:getPage')->setName('campaigns');
    $this->get('/campaigns/create', 'CampaignController:createCampaign')->setName('create_campaign');
    $this->post('/campaigns/create', 'CampaignController:postData')->setName('post_campaign');

    $this->get('/campaigns/{campaign_id}/update', 'CampaignController:updateCampaign')->setName('campaign');
    $this->post('/campaigns/{campaign_id}/update', 'CampaignController:postUpdate');
    $this->post('/campaign/{campaign_id}/activate', 'CampaignController:activateCampaign');
    $this->post('/campaign/{campaign_id}/deactivate', 'CampaignController:deactivateCampaign');
    
    $this->get('/upload', 'UploadController:getPage')->setName('upload');
    $this->post('/upload', 'UploadController:postData');

    $this->get('/file', 'FileController:getPage')->setName('files');

    $this->get('/settings', 'SettingsController:getPage')->setName('settings');
    $this->post('/settings', 'SettingsController:postData');

    $this->get('/logout', 'IndexController:logOut')->setName('logout');

    $this->get('/campaigns/{campaign_id}/report', 'ReportController:getCampaign')->setName('campaign_report');
    $this->get('/reports', 'ReportController:getPage')->setName('reports');

    // javascript data for pages
    $this->get('/dashboard', function($request, $response) {
        $ch = curl_init();
        $url = 'http://localhost:4043/elastic/elasticsearch/data';
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $res = curl_exec($ch);
        return $res;
    });

    // javascript data for today
    $this->get('/dashboard/today', function($request, $response) {
        $ch = curl_init();
        $url = 'http://localhost:4043/elastic/data/today';
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $res = curl_exec($ch);
        return $res;
    });

    // javascript data for yesterday
    $this->get('/dashboard/yesterday', function($request, $response) {
        $ch = curl_init();
        $url = 'http://localhost:4043/elastic/data/yesterday';
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $res = curl_exec($ch);
        return $res;
    });

    // javascript data for last week
    $this->get('/dashboard/last', function($request, $response) {
        $ch = curl_init();
        $url = 'http://localhost:4043/elastic/data/last';
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $res = curl_exec($ch);
        return $res;
    });

    // javascript data for last week
    $this->get('/dashboard/week', function($request, $response) {
        $ch = curl_init();
        $url = 'http://localhost:4043/elastic/data/week';
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $res = curl_exec($ch);
        return $res;
    });

    // javascript data for last week
    $this->get('/dashboard/month', function($request, $response) {
        $ch = curl_init();
        $url = 'http://localhost:4043/elastic/data/month';
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $res = curl_exec($ch);
        return $res;
    });

    $this->get('/campaign/period', function($request, $response) {
        $ch = curl_init();
        $url = 'http://localhost:4043/elastic/no_of_campaign';
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $res = curl_exec($ch);
        return $res;
    });

    $this->get('/campaign/{campaign_id}/data', function($request, $response, $args) {

        $campaign_id = $args['campaign_id'];

        if (!$campaign_id) {
            return null;
        }

        $ch = curl_init();
        $url = 'http://localhost:4043/elastic/campaign/'.$campaign_id.'/data';
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $res = curl_exec($ch);
        return $res;
    });

    $this->get('/reports/{campaign_id}/download', 'ReportController:DownloadCampaign')->setName('download_campaign');

    $this->post('/reports/{campaign_id}/download', 'ReportController:postDownloadCampaign');

    $this->get('/reports/download', 'ReportController:Download')->setName('download');

    $this->post('/reports/download', 'ReportController:postDownload');

    $this->post('/file/{file_id}/delete', 'FileController:deleteFile');

    $this->post('/record/filter', function($request, $response) {
        $ch = curl_init();
        $url = 'http://localhost:4043/elastic/record/filter';
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        $body = array(
            "start" => $request->getParam('start'),
            "end" => $request->getParam('end'),
        );

        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($body));
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $res = curl_exec($ch);
        return $res;
    });
    
})->add(new AuthMiddleware($container));

$app->group('/cdr', function (){

    $this->post('/create', function ($request, $response) {
        $connection=new AMQPStreamConnection('rabbit',5672,'guest','guest');
        $channel=$connection->channel();
        $channel->queue_declare('cdr',false,false,false,false);

        $data = array(
            "clid" => $request->getParam('clid'),
            "src" => $request->getParam('src'),
            "duration" => $request->getParam('duration'),
            "billsec" => $request->getParam('billsec'),
            "uniqueid" => $request->getParam('uniqueid'),
            "file_path" => $request->getParam('file_path'),
            "end_point" => 'create'
        );

        $msg=new AMQPMessage(json_encode($data));

        $channel->basic_publish($msg,'','cdr');

        return $response->withStatus(202);
    });

    $this->post('/impression', function ($request, $response) {
        $connection=new AMQPStreamConnection('rabbit',5672,'guest','guest');
        $channel=$connection->channel();
        $channel->queue_declare('cdr',false,false,false,false);

        $data = array(
            "end_point" => 'impression',
            "uniqueid" => $request->getParam('uniqueid')
        );

        $msg=new AMQPMessage(json_encode($data));
        $channel->basic_publish($msg,'','cdr');

        return $response->withStatus(202);
    });

    $this->post('/subscribe', function ($request, $response) {
        $connection=new AMQPStreamConnection('rabbit',5672,'guest','guest');
        $channel=$connection->channel();
        $channel->queue_declare('cdr',false,false,false,false);

        $data = array(
            "end_point" => 'subscribe',
            "uniqueid" => $request->getParam('uniqueid')
        );

        $msg=new AMQPMessage(json_encode($data));
        $channel->basic_publish($msg,'','cdr');

        return $response->withStatus(202);
    });

    $this->post('/confirmation', function ($request, $response) {
        $connection=new AMQPStreamConnection('rabbit',5672,'guest','guest');
        $channel=$connection->channel();
        $channel->queue_declare('cdr',false,false,false,false);

        $data = array(
            "end_point" => 'confirmation',
            "uniqueid" => $request->getParam('uniqueid')
        );

        $msg=new AMQPMessage(json_encode($data));
        $channel->basic_publish($msg,'','cdr');

        return $response->withStatus(202);
    });

    $this->post('/success', function ($request, $response) {
        $connection=new AMQPStreamConnection('rabbit',5672,'guest','guest');
        $channel=$connection->channel();
        $channel->queue_declare('cdr',false,false,false,false);

        $data = array(
            "end_point" => 'success',
            "uniqueid" => $request->getParam('uniqueid')
        );

        $msg=new AMQPMessage(json_encode($data));
        $channel->basic_publish($msg,'','cdr');

        return $response->withStatus(202);
    });

    $this->post('/insufficient', function ($request, $response) {
        $connection=new AMQPStreamConnection('rabbit',5672,'guest','guest');
        $channel=$connection->channel();
        $channel->queue_declare('cdr',false,false,false,false);

        $data = array(
            "end_point" => 'insufficient',
            "uniqueid" => $request->getParam('uniqueid')
        );

        $msg=new AMQPMessage(json_encode($data));
        $channel->basic_publish($msg,'','cdr');

        return $response->withStatus(202);
    });

    $this->post('/failed', function ($request, $response) {
        $connection=new AMQPStreamConnection('rabbit',5672,'guest','guest');
        $channel=$connection->channel();
        $channel->queue_declare('cdr',false,false,false,false);

        $data = array(
            "end_point" => 'failed',
            "uniqueid" => $request->getParam('uniqueid')
        );

        $msg=new AMQPMessage(json_encode($data));
        $channel->basic_publish($msg,'','cdr');

        return $response->withStatus(202);
    });

});