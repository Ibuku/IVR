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

use App\Models\Campaign;
use App\Models\Action;
use App\Services\Index;

$redis = new Client();

$campaigns = Campaign::all();

foreach ($campaigns as $campaign) {

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
