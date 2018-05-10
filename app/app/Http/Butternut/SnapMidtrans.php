<?php namespace App\Butternut;

use GuzzleHttp\Client;
use Exception;

/**
 * Class main task is to properly connect to midtrans snap api
 * 
 * @package butternut/snapmidtrans
 * @author SuperKandjeng
 * 
 */
class SnapMidtrans {
    private static $_ServerKey;
    
    /**
     * constructor for this class
     * 
     * @param String $serverKey
     * @param String $isProduction 
     * @return void
     */
    public function __construct($serverKey) {
        self::$_ServerKey = $serverKey;    
    }

    /**
     * private function, connecting and make http request to snap api
     * 
     * @param String $requestType either it's POST or GET
     * @param Array $requestBody the http request body which will be sent
     * @return PHP Type
     */
    private static function connectAPI($SnapUrl, $requestType, $requestBody = null) {
        $key = config('snapmidtrans.serverKey');
        try {
            $client = new Client();
            $response = $client->request($requestType, $SnapUrl, [
                    'headers' => [
                                'Content-Type' => 'application/json',
                                'Accept' => 'application/json',
                                'Authorization' => 'Basic ' . base64_encode($key. ':')
                            ],
                    'json' => $requestBody
                ]);

            return json_decode((string) $response->getBody());
        } catch (Exception $e) {
            throw new Exception ($e->getMessage() ,$e->getResponse()->getStatusCode());
        }
    }

    /**
     * get SNAP Token from midtrans API
     * 
     * @param Array $transaction detail transaction
     * @return String
     */
    public static function getSnapToken($transaction) {
        $SnapUrl = config('snapmidtrans.isProduction') ? config('snapmidtrans.urlProduction') : config('snapmidtrans.urlSandbox');
        return self::connectAPI($SnapUrl,'POST', $transaction)->token;
    }

    /** 
     * get midtrans client key 
     * 
     * @return String
    */
    public static function getClientKey(){
        return config('snapmidtrans.clientKey');
    }

    /** 
     * get midtrans frontend script url
     * 
     * @return String
    */
    public static function getClientScriptUrl(){
        return config('snapmidtrans.isProduction') ? config('snapmidtrans.clientUrlProduction') : config('snapmidtrans.clientUrlSandbox');
    }
    /**
     * get challenge url, return status code received from 
     * 
     * @return JSON
     */
    public static function getOrderStatus($transaction_id){
        $url = config('snapmidtrans.isProduction') ? config('snapmidtrans.challengUrlProduction') : config('snapmidtrans.challengeUrlSandbox');
        return self::connectAPI($url.'/'.$transaction_id.'/status','GET');
    }
}

