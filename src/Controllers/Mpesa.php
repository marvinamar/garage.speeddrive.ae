<?php
namespace Simcify\Controllers;

use Simcify\Database;
use Simcify\Asilify;
use Simcify\Auth;
use Simcify\Sms;

class Mpesa {
    
    /**
     * STK Push Callback
     * 
     * @return Json
     */
    public function callback() {
        
        $response = array(
            "ResultCode" => 0,
            "status" => "Accepted"
            );
        
        return response()->json($response);
        
    }
    
    /**
     * Validate mpesa transaction
     * 
     * @return Json
     */
    public function validate() {
        
        $response = array(
            "ResultCode" => 0,
            "ResultDesc" => "Accepted"
            );
        
        return response()->json($response);
        
    }
    
    /**
     * Confirm mpesa transaction
     * 
     * @return Json
     */
    public function confirm() {
        
        $payload = file_get_contents('php://input');
        $payment = json_decode($payload);
        
        $response = array(
            "txid" => $payment->TransID,
            "amount" => $payment->TransAmount,
            "txref" => $payment->BillRefNumber,
            "phonenumber" => $payment->MSISDN,
            "custname" => $payment->FirstName." ".$payment->MiddleName." ".$payment->LastName,
            "paymenttype" => "Mpesa",
            "status" => "New"
            );
            
        $response = $this->credit($payment, $response);
        
        Database::table('payments')->insert($response);
        
        $apiresponse = array(
            "ResultCode" => 0,
            "ResultDesc" => "Accepted"
            );
        
        return response()->json($apiresponse);
        
    }
    
    /**
     * Credit accounts
     * 
     * @return Json
     */
    public function credit($payment, $response) {
        
        $account = explode(".", $payment->BillRefNumber);
        if(isset($account[1])){
            
            $company = Database::table("companies")->where("id", $account[1])->first();
            
            if(empty($company)){
                return $response;
            }
            $response["company"] = $company->id;
            
            if(strtolower($account[0]) == "top"){
                
    			$balance = $payment->TransAmount + $company->sms_balance;
	            Database::table("companies")->where("id", $company->id)->update(array(
	                "sms_balance" => $balance
	            ));
	            
	            $response["type"] = "topup";
	            
	            Sms::africastalking($company->phone, "Hello, your Asilify account has been credited with ".$payment->TransAmount." SMS credits. New balance ".$balance." credits • Dotted Craft.");
	            
	            return $response;

            }elseif(strtolower($account[0]) == "sub" && isset($account[2])){
                
	            $response["type"] = "subscription";
                
                $plan = Asilify::plan($account[2]);
                
                if(!is_null($plan) && (int) $plan["price"] = (int) $response["amount"]){
                    
                    $period = Asilify::period($company, $plan["cycle"]);

                    $update = array(
                        "subscription_status" => "Active",
                        "subscription_start" => $period['start'],
                        "subscription_end" => $period['end'],
                        "subscription_plan" => $plan["name"]
                    );
    
                    $response["bill_for"] = $plan["name"]." Subscrition";
                    $response["subscription_plan"] = $plan["cycle"];
    	            Sms::africastalking($company->phone, "Hello, your Asilify subscription has been renewed. Your ".$plan["name"]." plan is valid from ".date("M j, Y", strtotime($period['start']))." upto ".date("M j, Y", strtotime($period['end']))." • Dotted Craft.");
    
                    Database::table("companies")->where("id", $company->id)->update($update);
                    
                }else{
                    
                    $this->uncredited($response["phonenumber"]);
                    
                }
                
                return $response;

            }else{
                
                $this->uncredited($response["phonenumber"]);
                
            }
            
        }else{
            
            $this->uncredited($response["phonenumber"]);
            
        }
        
        return $response;
        
    }
    
    /**
     * Uncredited Notification
     * 
     * @return Json
     */
    public function uncredited($phonenumber) {
        
        Sms::africastalking("+".$phonenumber, "Hello, your payment has been received but your Asilify account hasn't been credited. Please contact support to renew your subscription • Dotted Craft.");
        
    }
    
    /**
     * Verify payment
     * 
     * @return Json
     */
    public function verify() {
        
        $user = Auth::user();
        
        if(input("type") == "topup"){
            $payment = Database::table("payments")->where("company", $user->company)->where("status", "New")->where("type", "topup")->first();
        }elseif(input("type") == "subscription"){
            $payment = Database::table("payments")->where("company", $user->company)->where("status", "New")->where("type", "subscription")->first();
        }
        
        if(!empty($payment)){
            Database::table("payments")->where("id", $payment->id)->update(array(
                "status" => "Successful"
                ));
            
            if(input("type") == "topup"){
                return response()->json(responder("success", "Paid!", "Your payment has been received and your new balance ".$user->parent->sms_balance." is credits .", "reload()"));
            }elseif(input("type") == "subscription"){
                return response()->json(responder("success", "Paid!", "Your payment has been received and your ".$user->parent->subscription_plan." subscription has renewed valid upto ".date("M j, Y", strtotime($user->parent->subscription_end)).".", "reload()"));
            }
        }else{
            return response()->json(responder("warning", "Hmmm!", "We can't find a new transaction on your account. If this persists, please call 0720783834."));
        }
        
    }
    
    /**
     * Send STP Push
     * 
     * @return Json
     */
    public function stkpush() {

        $curl = curl_init();
        
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS =>'{
            "BusinessShortCode": "4047421",
            "Password": "'.base64_encode("4047421bb935aa65d49a0178f606d20ab32aa210d3ed24d459c996c85143e599a9b9f66".date("YmdHis")).'",
            "Timestamp": "'.date("YmdHis").'",
            "TransactionType": "CustomerPayBillOnline",
            "Amount": "'.input("amount").'",
            "PartyA": "'.str_replace("+","", input("PhoneNumber")).'",
            "PartyB": "4047421",
            "PhoneNumber": "'.str_replace("+","", input("PhoneNumber")).'",
            "CallBackURL": "https://app.asilify.com/payments/callback",
            "AccountReference": "'.input("AccountReference").'",
            "TransactionDesc": "Asilify payment."
        }',
          CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer '.$this->accessToken(),
            'Content-Type: application/json'
          ),
        ));
        
        $response = json_decode(curl_exec($curl));
        curl_close($curl);
        
        if($response->ResponseCode == "0"){
            
            $data = array(
                "MerchantRequestID" => $response->MerchantRequestID,
                "CheckoutRequestID" => $response->CheckoutRequestID
                );
                
            // Database::table('initiated_transactions')->insert($data);
            
            return response()->json(responder("info", "Check your phone!", "We've sent a M-Pesa payment request to your phone, please enter your PIN to complete the transaction."));
            
        }elseif(isset($response->errorMessage)){
            
            return response()->json(responder("error", "Hmmm!", $response->errorMessage));
            
        }else{
            
            return response()->json(responder("error", "Hmmm!", "Something went wrong, please try again."));
            
        }
        
        
        
        
    }
    
    /**
     * Get MPesa Access Token
     * 
     * @return Json
     */
    private function accessToken() {

        $curl = curl_init();
        
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => array(
            'Authorization: Basic '.base64_encode("kaf3KN0DA0TsW82fzUHcVlJGYTIJu6re:LHn0Wr7GHyXrcTZV")
          ),
        ));
        
        $response = json_decode(curl_exec($curl));
        
        curl_close($curl);
        
        return $response->access_token;

        
    }
    
}