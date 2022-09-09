<?php
namespace Simcify;

use Simcify\Sms;

class Asilify {

    /**
     * remove signature from a document
     * 
     * @param   int $id
     * @param   string $type
     * @return  void
     */
    public static function unsign($id, $type) {

        $unsign = array(
            "signed" => "No",
            "signature" => "NULL",
            "signed_by" => "NULL"
        );
            
        if ($type == "invoice") {
            Database::table('invoices')->where('id', $id)->update($unsign);
        }elseif ($type == "quote") {
            Database::table('quotes')->where('id', $id)->update($unsign);
        }

        return;

    }

    /**
     * Return zero of empty
     * 
     * @param   mixed $variable
     * @return  int
     */
    public static function zero($variable) {

        if (empty($variable)) {
            $variable = 0;
        }

        return $variable;

    }

    /**
     * Convert stacked input arrays to json
     * 
     * @param   array $array
     * @return  json
     */
    public static function array2json($array) {

      $array = array_map('trim', $array);
      $array = array_filter($array);
      $array = array_values($array);

      $clean = array();
      foreach ($array as $key => $value) {
        $clean[] = escape($value);
      }

      $json = json_encode($clean, JSON_HEX_QUOT | JSON_HEX_APOS);

      return $json;

    }
    
    /**
     * Save / Queue message
     * 
     * @return Json
     */
    public static function queue($receipient, $message, $user, $campaignid, $response = NULL) {

        $data = array(
            "company" => $user->company,
            "phonenumber" => escape($receipient["phonenumber"]),
            "name" => escape($receipient["name"]),
            "message" => escape($message),
            "campaign" => escape($campaignid)
        );

        if (!is_null($response)) {
            if ($response->status == "success") {
                $data["status"] = "Sent";
                $data["messageId"] = $response->data->SMSMessageData->Recipients[0]->messageId;
                
                if (isset($response->data->SMSMessageData->Recipients[0]->messageParts)) {
                  $data["messageParts"] = $response->data->SMSMessageData->Recipients[0]->messageParts;
                }else{
                  $data["messageParts"] = 1;
                }
                
                $data["cost"] = str_replace("KES ", "", $response->data->SMSMessageData->Recipients[0]->cost);

                self::smsbalance($user->company, $data["cost"]);
            }else{
                $data["status"] = "Failed";
            }
            
        }else{
            $data["status"] = "Queued";
        }

        Database::table('messages')->insert($data);

    }
    
    /**
     * Check & Update SMS balance
     * 
     * @return Json
     */
    public static function smsbalance($companyid, $cost = NULL) {

        $company = Database::table("companies")->where("id", $companyid)->first();

        if (is_null($cost)) {
            return $company->sms_balance;
        }else{

            $balance = $company->sms_balance - $cost;

            Database::table("companies")->where("id", $companyid)->update(array(
                "sms_balance" => $balance
            ));

            return;

        }

    }
    
    /**
     * Validate flutterwave transaction
     * 
     * @return Json
     */
    public static function transaction($reference) {

        $data = array(
            'txref' => $reference,
            'SECKEY' => env("FLUTTER_SECKEY")
        );

        $headers = array('Content-Type' => 'application/json');
        $body = \Unirest\Request\Body::json($data);
        $url = "https://api.ravepay.co/flwv3-pug/getpaidx/api/v2/verify";

        $response = \Unirest\Request::post($url, $headers, $body);

        if ($response->code == 200) {
            $response = $response->body;
        }else{
            return array(
                "status" => "Failed",
            );
        }

        if ($response->status == "success") {
            return array(
                "status" => $response->status,
                "txid" => $response->data->txid,
                "txref" => $response->data->txref,
                "amount" => $response->data->amount,
                "appfee" => $response->data->appfee,
                "ip" => $response->data->ip,
                "paymenttype" => $response->data->paymenttype,
                "customerid" => $response->data->customerid,
                "custname" => $response->data->custname,
                "custemail" => $response->data->custemail,
                "created" => $response->data->created,
                "paymentid" => $response->data->paymentid,
            );

        }else{
            return array(
                "status" => "Failed",
            );
        }

    }
    
    /**
     * Find flutterwave subscription
     * 
     * @return array
     */
    public static function subscription($transaction_id) {

        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://api.flutterwave.com/v3/subscriptions?transaction_id=".$transaction_id,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_HTTPHEADER => array(
            "Authorization: ".env("FLUTTER_SECKEY")
          ),
        ));

        $response = json_decode(curl_exec($curl));

        curl_close($curl);

        return $response->data;

    }
    
    /**
     * Get the name of a plan
     * 
     * @return array
     */
    public static function plan($cycle) {

        $plans = array(
            "monthly" => array(
                "name" => "Premium Monthly",
                "price" => env("PRICE_MONTHLY"),
                "cycle" => "monthly"
            ),
            "biannually" => array(
                "name" => "Premium Biannually",
                "price" => env("PRICE_BIANNUALLY"),
                "cycle" => "biannually"
            ),
            "annually" => array(
                "name" => "Premium Annually",
                "price" => env("PRICE_ANNUALLY"),
                "cycle" => "annually"
            ),
            "m" => array(
                "name" => "Premium Monthly",
                "price" => env("PRICE_MONTHLY"),
                "cycle" => "monthly"
            ),
            "b" => array(
                "name" => "Premium Biannually",
                "price" => env("PRICE_BIANNUALLY"),
                "cycle" => "biannually"
            ),
            "a" => array(
                "name" => "Premium Annually",
                "price" => env("PRICE_ANNUALLY"),
                "cycle" => "annually"
            )
        );
        
        if(isset($plans[$cycle])){
            return $plans[$cycle];
        }else{
            return NULL;
        }
        
    }
    
    /**
     * Get plan start end date
     * 
     * @return date (string)
     */
    public static function period($company, $cycle) {
        
        if($company->subscription_status == "Active" || $company->subscription_status == "Cancelled"){
            $start = $company->subscription_end;
        }else{
            $start = date('Y-m-d');
        }

        if ($cycle == "monthly") {
            $end = date('Y-m-d', strtotime($start. ' + 30 days'));
        }elseif ($cycle == "biannually") {
            $end = date('Y-m-d', strtotime($start. ' + 180 days'));
        }elseif ($cycle == "annually") {
            $end = date('Y-m-d', strtotime($start. ' + 365 days'));
        }

        return array(
            "start" => $start,
            "end" => $end
            );
        
    }
    
    /**
     * List all subscriptions from flutterwave
     * 
     * @return Object
     */
    public static function subscriptions() {

        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://api.flutterwave.com/v3/subscriptions",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_HTTPHEADER => array(
            "Authorization: ".env("FLUTTER_SECKEY")
          ),
        ));

        $response = json_decode(curl_exec($curl));

        curl_close($curl);
        
        if(isset($response->data)){
            return $response->data;
        }else{
            return array();
        }

        
    }
    
    /**
     * Sync Booking part
     * 
     * @param $companyid ( int ) 
     * @return Void 
     */
    public static function syncbookingparts($companyid) {

      $partslist = Database::table("partslist")->where("template", "Yes")->get();

      if (!empty($partslist)) {
        foreach ($partslist as $key => $part) {

          Database::table('partslist')->insert(array(
                    "name" => $part->name,
                    "company" => $companyid
          ));

        }
      }

    }

    

}

