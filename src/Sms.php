<?php
namespace Simcify;

use AfricasTalking\SDK\AfricasTalking;
use Twilio\Rest\Client;

class Sms {

    /**
     * Send SMS With Africa's Talking
     * 
     * @param   string $phone number
     * @param   string $message
     * @return  array
     */
    public static function africastalking($phoneNumber,$message) {

        if (empty(env('AFRICASTALKING_USERNAME')) || empty(env('AFRICASTALKING_KEY'))) {
          return ( object ) array(
            "status" => "error"
          );
        }

        $username = env('AFRICASTALKING_USERNAME'); 
        $apiKey   = env('AFRICASTALKING_KEY'); 
        $from   = env('AFRICASTALKING_SENDERID');
        $AT       = new AfricasTalking($username, $apiKey);
        
        // Get one of the services
        $sms      = $AT->sms();
        
        if(empty($from)){
           // Use the service
            $response   = $sms->send([
                'to'      => $phoneNumber,
                'message' => $message
            ]); 
        }else{
            // Use the service
            $response   = $sms->send([
                'to'      => $phoneNumber,
                'message' => $message,
                'from' => $from
            ]);
        }

        return (object) $response;

    }

    /**
     * Get Africa's Talking balance
     * 
     * @return  array
     */
    public static function africastalkingBalance() {
        
        $response = array();
        
        if (empty(env('AFRICASTALKING_USERNAME')) || empty(env('AFRICASTALKING_KEY'))) {
          $response['status'] = "error";
          $response['errorMessage'] = "API keys not set";
          return $response;
        }

        $AT    = new AfricasTalking(env('AFRICASTALKING_USERNAME'), env('AFRICASTALKING_KEY'));
        $application = $AT->application();
        
        try{ 
          $result = $application->fetchApplicationData();

          $response['balance'] = $result['data']->UserData->balance;
          $response['status'] = "success";
        }
        catch ( AfricasTalkingGatewayException $e ){
          $response['status'] = "error";
          $response['errorMessage'] = "Encountered an error while fetching user data: ".$e->getMessage()."\n";;
        }
        
        return $response;

    }

    /**
     * Send SMS With Twilio
     * 
     * @param   string $phone number
     * @param   string $message
     * @return  array
     */
    public static function twilio($phoneNumber,$message) {

        $client = new Client(env('TWILIO_SID'), env('TWILIO_AUTHTOKEN'));
        try{ 
            $response = $client->account->messages->create(
                $phoneNumber,
                array(
                    'from' => env('TWILIO_PHONENUMBER'), 
                    'body' => $message
                )
            );
          }catch(\Exception $e){  
            return false;
          }

        if ($response->status == "sent" || $response->status == "queued") {
            return true;
        }else{
            return false;
        }
    }
}

