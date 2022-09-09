<?php
namespace Simcify\Controllers;

use Simcify\Database;
use Simcify\Asilify;
use Simcify\Auth;
use Simcify\Sms;

class Marketing {
    
    /**
     * Render marketing page
     * 
     * @return \Pecee\Http\Response
     */
    public function get() {
        
        $title = "Marketing";
        $user = Auth::user();

        $members = Database::table("users")->where("company", $user->company)->orderBy("id", false)->get();
        $clients = Database::table("clients")->where("company", $user->company)->orderBy("id", false)->get();
        $campaigns = Database::table("campaigns")->where("company", $user->company)->orderBy("id", false)->get();

        $makes = Database::table('makes')->where('status', "Enabled")->orderBy("id", false)->get();

        foreach ($campaigns as $key => $campaign) {
            $campaign->cost = Database::table('messages')->where('campaign', $campaign->id)->where("status","!=", "Failed")->sum("cost", "total")[0]->total;
            $campaign->sent = Database::table('messages')->where('campaign', $campaign->id)->where("status", "Sent")->count("id", "total")[0]->total;
            $campaign->messages = Database::table('messages')->where('campaign', $campaign->id)->count("id", "total")[0]->total;
        }
        
        return view('marketing', compact("user", "title", "members", "clients", "campaigns","makes"));
        
    }
    
    /**
     * Render marketing page
     * 
     * @return \Pecee\Http\Response
     */
    public function recipients() {
        
        $user = Auth::user();

        if (isset($_GET["campaign"]) && !empty($_GET["campaign"])) {
            $campaign = Database::table("campaigns")->where("company", $user->company)->where("id", $_GET["campaign"])->first();
            $messages = Database::table("messages")->where("company", $user->company)->where("campaign", $_GET["campaign"])->orderBy("id", false)->get();
            $title = "Campaign recipients";
        }else{
            $messages = Database::table("messages")->fetch("SELECT * FROM `messages` WHERE 1 AND `company` = ".$user->company." AND `campaign` IS NULL ORDER BY `id` DESC
");
            $campaign = NULL;
            $title = "SMS History";
        }
        
        return view('campaign-recipients', compact("user", "title", "messages", "campaign"));
        
    }
    
    /**
     * Create a campaign
     * 
     * @return Json
     */
    public function create() {

        $user = Auth::user();
        $message = input("message")."\n\n ".$user->parent->name;

        if ($user->parent->sms_balance < 0) {
            return response()->json(responder("error", "Insufficient Credits!", "You have ".$user->parent->sms_balance." SMS credits. Top up and send again."));
        }

        if (isset($_POST["title"])) {
            $data = array(
                "company" => $user->company,
                "title" => escape(input('title')),
                "message" => escape($message),
                "sendto" => escape(input('sendto')),
                "status" => "Queued"
            );
        }

        if (input("sendto") == "enternumber") {
            $data["status"] = "Completed";
        }

        if (isset($_POST["title"])) {
            Database::table('campaigns')->insert($data);
            $campaignid = Database::table('campaigns')->insertId();
        }else{
            $campaignid = "NULL";
        }

        if (input("sendto") == "enternumber") {
           // $response = Sms::africastalking(input("phonenumber"), $message);
           $response = Sms::twilio(input("phonenumber"), $message);

            if (!empty(input("name"))) {
                $name = input("name");
            }else{
                $name = "NULL";
            }

            Asilify::queue(array(
                        "phonenumber" => input("phonenumber"),
                        "name" => $name
                    ), $message, $user, $campaignid, $response);
        }elseif (input("sendto") == "clients") {
            $clients = Database::table("clients")->where("company", $user->company)->orderBy("id", false)->get();
            foreach ($clients as $key => $client) {
                if (!empty($client->phonenumber)) {
                    Asilify::queue(array(
                        "phonenumber" => $client->phonenumber,
                        "name" => $client->fullname
                    ), $message, $user, $campaignid);
                }
            }
        }elseif (input("sendto") == "members") {
            $members = Database::table("users")->where("company", $user->company)->orderBy("id", false)->get();
            foreach ($members as $key => $member) {
                if (!empty($member->phonenumber)) {
                    Asilify::queue(array(
                        "phonenumber" => $member->phonenumber,
                        "name" => $member->fname." ".$member->lname
                    ), $message, $user, $campaignid);
                }
            }
        }elseif (input("sendto") == "selectedclients") {
            $clients = Database::table("clients")->where("company", $user->company)->where("id", "IN", "(".implode(",", $_POST["clients"]).")")->orderBy("id", false)->get();
            foreach ($clients as $key => $client) {
                if (!empty($client->phonenumber)) {
                    Asilify::queue(array(
                        "phonenumber" => $client->phonenumber,
                        "name" => $client->fullname
                    ), $message, $user, $campaignid);
                }
            }
        }elseif (input("sendto") == "selectedmembers") {
            $members = Database::table("users")->where("company", $user->company)->where("id", "IN", "(".implode(",", $_POST["members"]).")")->orderBy("id", false)->get();
            foreach ($members as $key => $member) {
                if (!empty($member->phonenumber)) {
                    Asilify::queue(array(
                        "phonenumber" => $member->phonenumber,
                        "name" => $member->fname." ".$member->lname
                    ), $message, $user, $campaignid);
                }
            }
        }elseif (input("sendto") == "filterbycar") {

            if (!empty(input("make")) && !empty(input("model"))) {
                $projects = Database::table('projects')->where('make', input("make"))->where('model', input("model"))->get("client");
            }elseif (!empty(input("make"))) {
                $projects = Database::table('projects')->where('make', input("make"))->get("client");
            }else{
                $projects = array();
            }

            $clientids = array(0);
            foreach ($projects as $key => $project) {
                if (!in_array($project->client, $clientids)) {
                    $clientids[] = $project->client;
                }
            }

            $clients = Database::table("clients")->where("company", $user->company)->where("id", "IN", "(".implode(",", $clientids).")")->orderBy("id", false)->get();
            foreach ($clients as $key => $client) {
                if (!empty($client->phonenumber)) {
                    Asilify::queue(array(
                        "phonenumber" => $client->phonenumber,
                        "name" => $client->fullname
                    ), $message, $user, $campaignid);
                }
            }

        }else{
            return response()->json(responder("error", "Hmmm!", "Something went wrong, please try again."));
        }
        
        if (input("sendto") == "enternumber") {
            return response()->json(responder("success", "Alright!", "Message successfully sent.", "reload()"));
        }else{
            return response()->json(responder("success", "Alright!", "Campaign successfully queued and will start sending now.", "reload()"));
        }
        
        
    }
    
    /**
     * Resend message
     * 
     * @return Json
     */
    public function resend() {

        $user = Auth::user();

        $message = Database::table("messages")->where("company", $user->company)->where("id", input("messageid"))->first();

        // $response = Sms::africastalking($message->phonenumber, $message->message);
        $response = Sms::twilio($message->phonenumber, $message->message);

        if ($response->status == "success") {
            $data["status"] = "Sent";
            $data["messageId"] = $response->data->SMSMessageData->Recipients[0]->messageId;
            $data["messageParts"] = $response->data->SMSMessageData->Recipients[0]->messageParts;
            $data["cost"] = str_replace("KES ", "", $response->data->SMSMessageData->Recipients[0]->cost);
        }else{
            $data["status"] = "Failed";
        }

        Database::table('messages')->where("company", $user->company)->where("id", input("messageid"))->update($data);
        
        if ($response->status == "success") {
            return response()->json(responder("success", "Alright!", "Message successfully sent.", "reload()"));
        }else{
            return response()->json(responder("error", "Hmmm!", "Failed to send message, please try again."));
        }
        
    }
    
    
    /**
     * Delete a campaign
     * 
     * @return Json
     */
    public function delete() {
        
        $user = Auth::user();
        Database::table('campaigns')->where('id', input('campaignid'))->where('company', $user->company)->delete();
        
        return response()->json(responder("success", "Alright!", "Campaign successfully deleted.", "reload()"));
        
    }
    
    
    /**
     * Delete a message
     * 
     * @return Json
     */
    public function deletemessage() {
        
        $user = Auth::user();
        Database::table('messages')->where('id', input('messageid'))->where('company', $user->company)->delete();
        
        return response()->json(responder("success", "Alright!", "Message successfully deleted.", "reload()"));
        
    }
    
    
    /**
     * Account balances
     * 
     * @return \Pecee\Http\Response
     */
    public function balance() {
        
        $user   = Auth::user();
        
        // $response = Sms::africastalkingBalance();
                
        if($response['status'] == "success"){
            $smsbalance = $response['balance'];
        }else{
            $smsbalance = "-|-";
        }
        
        
        return view('modals/balance', compact("user","smsbalance"));
        
    }
    
}