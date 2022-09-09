<?php
namespace Simcify;

use Simcify\Auth;
use Simcify\Database;

class Mpesa {
    
    
	/**
	 * Submit Request
	 * 
	 * Handles submission of all API endpoints queries
	 * 
	 * @param string $url The API endpoint URL
	 * @param json $data The data to POST to the endpoint $url
	 * @return object|boolean Curl response or FALSE on failure
	 * @throws exception if the Access Token is not valid
	 */

	private static function sendRequest($url, $data){ 
		
		if(!empty(env("MPESA_B2C_ACCESS_TOKEN"))){
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, "https://api.safaricom.co.ke/mpesa/".$url);
			curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Authorization: Bearer '.env("MPESA_B2C_ACCESS_TOKEN")));
			
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($curl, CURLOPT_POST, TRUE);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
			
			$response = curl_exec($curl);
			curl_close($curl);
			return $response;
		}else{
			return FALSE;
		}
	}
	/**
	 * Send money (Business to Client)
	 * 
	 * This method is used to send money to the clients Mpesa account.
	 * 
	 * @param int $amount The amount to send to the client
	 * @param int $phone The phone number of the client in the format 2547xxxxxxxx
	 * @return object Curl Response from submit_request, FALSE on failure
	 */

	public static function send($amount, $phone, $reference = ''){
		$request_data = array(
			'InitiatorName' => env("MPESA_B2C_USERNAME"),
			'SecurityCredential' => self::credentials(),
			'CommandID' => 'BusinessPayment',
			'Amount' => $amount,
			'PartyA' => env("MPESA_B2C_PAYBILL"),
			'PartyB' => $phone,
			'Remarks' => 'Send money to phone number',
			'QueueTimeOutURL' => "https://app.walletmatic.com/api/safaricom/timeout",
			'ResultURL' => "https://app.walletmatic.com/api/safaricom/b2c/callback?reference=".$reference,
			'Occasion' => '' //Optional
		);
		$data = json_encode($request_data);
		$url = 'b2c/v1/paymentrequest';
		$response = self::sendRequest($url, $data);
		return $response;
	}
    
	/**
	 * Check Balance
	 * 
	 * Check Paybill balance
	 * 
	 * @return object Curl Response from sendRequest, FALSE on failure
	 */
	public static function balance(){
		$data = array(
			'CommandID' => 'AccountBalance',
			'PartyA' => env("MPESA_B2C_PAYBILL"),
			'IdentifierType' => '4',
			'Remarks' => 'Remarks or short description',
			'Initiator' => env("MPESA_B2C_USERNAME"),
			'SecurityCredential' => self::credentials(),
			'QueueTimeOutURL' => "https://app.walletmatic.com/api/safaricom/timeout",
			'ResultURL' => "https://app.walletmatic.com/api/safaricom/balance/callback"
		);
		$data = json_encode($data);
		$url = 'accountbalance/v1/query';
		$response = self::sendRequest($url, $data);
		return $response;
	}

	/**
	 * Transaction status request
	 * 
	 * This method is used to check a transaction status
	 * 
	 * @param string $transaction ID eg LH7819VXPE
	 * @return object Curl Response from submit_request, FALSE on failure
	 */
	 
	public static function status($transaction){
		$data = array(
			'CommandID' => 'TransactionStatusQuery',
			'PartyA' => env("MPESA_B2C_PAYBILL"),
			'IdentifierType' => 4,
			'Remarks' => 'Confirming status for ID: '.$transaction,
			'Initiator' => env("MPESA_B2C_USERNAME"),
			'SecurityCredential' => self::credentials(),
			'QueueTimeOutURL' => "https://app.walletmatic.com/api/safaricom/timeout",
			'ResultURL' => "https://app.walletmatic.com/api/safaricom/status/callback",
			'TransactionID' => $transaction,
			'Occassion' => ''
		);
		$data = json_encode($data);
		$url = 'transactionstatus/v1/query';
		$response = self::sendRequest($url, $data);
		return $response;
	}

	/**
	 * Credentials
	 * 
	 * This method returns signed mpesa initiator password
	 * 
	 * @return string 256 encoded
	 */
	 
	private static function credentials(){
	    
		$publicKey = file_get_contents(config("app.storage")."app/mpesa.cer");

		openssl_public_encrypt(env("MPESA_B2C_PASSWORD"), $signature, $publicKey, OPENSSL_PKCS1_PADDING);

		$credentials = base64_encode($signature);
		
		return $credentials;
		
	}
    
}