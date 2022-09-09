<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

include_once 'vendor/autoload.php';

use Simcify\Application;
use Simcify\Database;
use Simcify\Asilify;
use Simcify\Auth;
use Simcify\Mail;
use Simcify\Sms;


$app = new Application();

$today = date("Y-m-d");


/**
 * Send queued messages
 * 
 */
$messages = Database::table("messages")->where("status", "Queued")->orderBy("id", false)->get();
if (!empty($messages)) {
	foreach ($messages as $key => $message) {

		$data = array();

		$company = Database::table("companies")->where("id", $message->company)->first();
		if ($company->sms_balance <= 0) {
			Database::table('messages')->where("id", $message->id)->update(array(
				"status" => "Failed"
			));

			continue;
		}

        $response = Sms::africastalking($message->phonenumber, $message->message);

        if ($response->status == "success") {
            $data["status"] = "Sent";
            $data["messageId"] = $response->data->SMSMessageData->Recipients[0]->messageId;
            $data["messageParts"] = $response->data->SMSMessageData->Recipients[0]->messageParts;
            $data["cost"] = str_replace("KES ", "", $response->data->SMSMessageData->Recipients[0]->cost);

            Asilify::smsbalance($message->company, $data["cost"]);
        }else{
            $data["status"] = "Failed";
        }

        Database::table('messages')->where("id", $message->id)->update($data);
        
	}

}

/**
 * Mark sending campaign as complete when completed
 * 
 */
$campaigns = Database::table("campaigns")->where("status", "Queued")->orderBy("id", false)->get();
if (!empty($campaigns)) {
	foreach ($campaigns as $key => $campaign) {
		$queuedmessages = Database::table("messages")->where("campaign", $campaign->id)->where("status", "Queued")->count("id","total")[0]->total;
		if ($queuedmessages == 0) {
			Database::table('campaigns')->where("id", $campaign->id)->update(array("status" => "Completed"));
		}
	}
}













