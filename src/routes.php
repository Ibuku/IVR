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


//$app->get('/cdr/success', function($request, $response){
//
//    $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
//    $channel = $connection->channel();
//    $channel->exchange_declare('ivr', 'headers', false, true, false);
//    $channel->queue_declare('ivr', false, true, false, false);
//    $channel->queue_bind('ivr', 'ivr');
//
//    $data = array('clid' => $request->getParam('clid'),
//        'serv_id'=> $serviceID,
//        'scrm' => $srcModule,
//        'dlr' => $dlr,
//        'sender_id' => $senderID,
//        'smsc' => $smsc,
//        'request_time'=>$requestTime
//    );
//    $_data = json_encode($data);
//
//    $msg = new AMQPMessage($_data);
//    $channel->basic_publish($msg, '', $que);
//});

$app->group('', function (){
    
    $this->get('/login', 'LoginController:getPage')->setName('login');
    
    $this->post('/login', 'LoginController:postData');

})->add(new GuestMiddleware($container));
    
$app->group('', function (){

    $this->get('/', 'IndexController:index')->setName('index');

    $this->get('/campaigns', 'CampaignController:getPage')->setName('campaigns');

    $this->get('/campaigns/create', 'CampaignController:createCampaign')->setName('create_campaign');

    $this->post('/campaigns/create', 'CampaignController:postData')->setName('post_campaign');

    $this->get('/campaigns/{campaign_id}/update', 'CampaignController:updateCampaign')->setName('campaign');

    $this->post('/campaigns/{campaign_id}/update', 'CampaignController:postUpdate');
    
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

    $this->post('/campaign/{campaign_id}/deactivate', 'CampaignController:deactivateCampaign');
    
})->add(new AuthMiddleware($container));

$app->group('/cdr', function (){

    $this->post('/create', function ($request, $response) {
        $connection=new AMQPStreamConnection('localhost',5672,'guest','guest');
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
        $connection=new AMQPStreamConnection('localhost',5672,'guest','guest');
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
        $connection=new AMQPStreamConnection('localhost',5672,'guest','guest');
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
        $connection=new AMQPStreamConnection('localhost',5672,'guest','guest');
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
        $connection=new AMQPStreamConnection('localhost',5672,'guest','guest');
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
        $connection=new AMQPStreamConnection('localhost',5672,'guest','guest');
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
        $connection=new AMQPStreamConnection('localhost',5672,'guest','guest');
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