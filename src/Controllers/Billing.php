<?php
namespace Simcify\Controllers;

use Simcify\Database;
use Simcify\Asilify;
use Simcify\Auth;
use Simcify\Sms;

class Billing {
    
    /**
     * Render billing page
     * 
     * @return \Pecee\Http\Response
     */
    public function get() {

      return view('errors/404');

    	$user = Auth::user();
    	$title = "Billing";
        
      if ($user->role == "Staff" || $user->role == "Inventory Manager" || $user->role == "Booking Manager") {
          return view('errors/404');
      }

    	$payments = Database::table('payments')->where('company', $user->company)->orderBy("id", false)->limit(3)->get();

        return view("billing", compact("user", "title", "payments"));

    }
    
    /**
     * Render billing page
     * 
     * @return \Pecee\Http\Response
     */
    public function payments() {

    	$user = Auth::user();
    	$title = "Billing Payments";

    	$payments = Database::table('payments')->where('company', $user->company)->orderBy("id", false)->get();

        return view("billing-payments", compact("user", "title", "payments"));

    }
    
    /**
     * Cancel a subscription plan
     * 
     * @return \Pecee\Http\Response
     */
    public function cancel() {

      $user = Auth::user();

      if (!hash_compare($user->password, Auth::password(input("password")))) {
        return response()->json(responder("error", "Oops", "You have entered an incorrect password."));
      }

      $company = array(
          "subscription_status" => "Cancelled"
      );

      Database::table("companies")->where("id", $user->company)->update($company);

      return response()->json(responder("success", "Subscription cancelled!", "Your subscription is successfully cancelled.","reload()"));

    }
    
    /**
     * Verify a transaction
     * 
     * @return \Pecee\Http\Response
     */
    public function verify($type) {

    	$user = Auth::user();

    	if (isset($_GET["status"]) && $_GET["status"] == "successful" && isset($_GET["tx_ref"]) &&  !empty($_GET["tx_ref"])) {

    		$response =Asilify::transaction($_GET["tx_ref"]);

    		$payment = Database::table("payments")->where("txid", $response["txid"])->first();
    		if (!empty($payment)) {
    			if ($type == "topup" || $type == "t") {
	    			redirect(url("Marketing@get")."?payment=error");
	    		}elseif ($type == "subscription" || $type == "s") {
                    redirect(url("Billing@get")."?payment=error");
                }
    		}

    		if ($response["status"] == "success") {

	    		$response["type"] = $type;
	    		$response["company"] = $user->company;
	    		$response["user"] = $user->id;
	    		
	    		$company = Database::table("companies")->where("id", $user->company)->first();

	    		if ($type == "topup" || $type == "t") {

	    			$balance = $response["amount"] + $company->sms_balance;
		            Database::table("companies")->where("id", $user->company)->update(array(
		                "sms_balance" => $balance
		            ));
	    		}elseif ($type == "subscription" || $type == "s") {
                    $subscription =Asilify::subscription($response["txid"]);

                    if (empty($subscription)) {
                        redirect(url("Billing@get")."?payment=error");
                    }else{
                        $subscription = $subscription[0];

                        $plan = Asilify::plan($response["amount"]);
                        $period = Asilify::period($company, $plan["cycle"]);

                        $update = array(
                            "subscription_status" => "Active",
                            "subscription_start" => $period['start'],
                            "subscription_end" => $period['end'],
                            "subscription_plan" => $plan["name"]
                        );

                        $response["bill_for"] = $plan["name"]." Subscrition";
                        $response["subscription_plan"] = $plan["cycle"];

                        Database::table("companies")->where("id", $user->company)->update($update);

                    }
                }

    			Database::table("payments")->insert($response);

				if ($type == "topup" || $type == "t") {
	    			redirect(url("Marketing@get")."?payment=success");
	    		}elseif ($type == "subscription" || $type == "s") {
                    redirect(url("Billing@get")."?payment=success");
                }

    		}

    	}else{
			if ($type == "topup" || $type == "t") {
    			redirect(url("Marketing@get")."?payment=error");
    		}elseif ($type == "subscription" || $type == "s") {
                redirect(url("Billing@get")."?payment=error");
            }
    	}

		die();

    }

}