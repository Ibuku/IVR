<?php
/**
 * Created by PhpStorm.
 * User: stikks-workstation
 * Date: 1/18/17
 * Time: 12:39 PM
 */
date_default_timezone_set('Africa/Lagos');
require __DIR__ . '/bootstrap/app.php';
require 'vendor/autoload.php';
use Predis\Client;
use App\Models\Action;
use App\Services\Index;

$actions = Action::all();

foreach ($actions as $act) {

    $campaign = json_decode($camp);
    $_data = [
        'username' => $campaign->username,
        'start_date' => $campaign->start_date,
        'end_date' => $campaign->end_date,
        'name' => $campaign->name,
        'file_path' => $campaign->file_path,
        'play_path' => $campaign->play_path,
        'description' => $campaign->description,
        'id' => $campaign->id,
        'created_at' => $campaign->created_at,
        'updated_at' => $campaign->updated_at,
        'is_active' => $campaign->is_active
    ];

    Index::save_redis($campaign->play_path, $_data);

    $action = Action::where('campaign_id', $campaign->id)->first();
    if ($action) {
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
}

$redis = new Client();

$data = [
    '{"username":"tm30","start_date":"2016-11-28","end_date":"2016-12-10","name":"Football Icon ","file_path":"\/opt\/ivr\/files\/tm30\/005.wav","play_path":"\/var\/lib\/asterisk\/sounds\/files\/tm30\/005.wav","description":"Get  Football Facts about your favorite football players, free for 5days\r\n","id":25,"created_at":"2016-11-28","updated_at":"2016-11-28","is_active":"1"}',
    '{"username":"tm30","start_date":"2016-11-28","end_date":"2016-12-10","name":"Career Tips","file_path":"\/opt\/ivr\/files\/tm30\/007.wav","play_path":"\/var\/lib\/asterisk\/sounds\/files\/tm30\/007.wav","description":"Grab this wonderful opportunity to receive daily Career tips, Free for 5days.","id":26,"created_at":"2016-11-28","updated_at":"2016-11-28","is_active":"1"}',
    '{"username":"tm30","start_date":"2016-11-28","end_date":"2016-12-01","name":"Wedding Tips","file_path":"\/opt\/ivr\/files\/tm30\/006.wav","play_path":"\/var\/lib\/asterisk\/sounds\/files\/tm30\/006.wav","description":"Get Daily tips on How to plan the next big Wedding, Free for 5days.\r\n","id":24,"created_at":"2016-11-28","updated_at":"2016-12-01","is_active":"0"}',
    '{"username":"tm30","start_date":"2016-11-28","end_date":"2016-12-01","name":"Social Media Engagement Tips","file_path":"\/opt\/ivr\/files\/tm30\/008a.wav","play_path":"\/var\/lib\/asterisk\/sounds\/files\/tm30\/008a.wav","description":"Increase your followership with Daily Tips on Social Media Engagement, Free  for 5days","id":23,"created_at":"2016-11-28","updated_at":"2016-12-01","is_active":"0"}',
    '{"username":"etisalat","start_date":"2016-11-28","end_date":"2016-12-01","name":" Honda Maintenance tips","file_path":"\/opt\/ivr\/files\/etisalat\/001.wav","play_path":"\/var\/lib\/asterisk\/sounds\/files\/etisalat\/001.wav","description":"Subscribe to Honda Maintenance tips at N50 for 5days","id":19,"created_at":"2016-11-28","updated_at":"2016-12-01","is_active":"0"}',
    '{"username":"etisalat","start_date":"2016-11-28","end_date":"2016-12-01","name":"Job openings & interview tips","file_path":"\/opt\/ivr\/files\/etisalat\/002.wav","play_path":"\/var\/lib\/asterisk\/sounds\/files\/etisalat\/002.wav","description":"Enjoy job openings & interview tips at N50 for 7day","id":20,"created_at":"2016-11-28","updated_at":"2016-12-01","is_active":"0"}',
    '{"username":"etisalat","start_date":"2016-11-28","end_date":"2016-12-02","name":"Funniest jokes from your favorites comedians","file_path":"\/opt\/ivr\/files\/etisalat\/003.wav","play_path":"\/var\/lib\/asterisk\/sounds\/files\/etisalat\/003.wav","description":"Get the funniest jokes from your favorites comedians for just N10 daily","id":21,"created_at":"2016-11-28","updated_at":"2016-11-28","is_active":"1"}',
    '{"username":"etisalat","start_date":"2016-11-28","end_date":"2016-12-02","name":"Natural hair, skin and beauty tips","file_path":"\/opt\/ivr\/files\/etisalat\/004.wav","play_path":"\/var\/lib\/asterisk\/sounds\/files\/etisalat\/004.wav","description":"Enjoy natural hair, skin and beauty tips for just N10 daily","id":22,"created_at":"2016-11-28","updated_at":"2016-11-28","is_active":"1"}'
];

foreach ($data as $camp) {

    $campaign = json_decode($camp);
    $_data = [
        'username' => $campaign->username,
        'start_date' => $campaign->start_date,
        'end_date' => $campaign->end_date,
        'name' => $campaign->name,
        'file_path' => $campaign->file_path,
        'play_path' => $campaign->play_path,
        'description' => $campaign->description,
        'id' => $campaign->id,
        'created_at' => $campaign->created_at,
        'updated_at' => $campaign->updated_at,
        'is_active' => $campaign->is_active
    ];

    Index::save_redis($campaign->play_path, $_data);

    $action = Action::where('campaign_id', $campaign->id)->first();
    if ($action) {
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
}
