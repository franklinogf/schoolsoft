<?php

namespace App\Services;

use Classes\Session;

class EvertecPayment
{
    private $creditCardEndpoint;
    private $achEndpoint;
    private $username;
    private $password;
    private $prefix;

    public function __construct(?string $prefix = null, bool $isDevelopment = false)
    {
        $this->prefix = $prefix;

        // Set endpoints based on environment
        if ($isDevelopment) {
            $this->creditCardEndpoint = 'https://uat.mmpay.evertecinc.com/WebPaymentAPI/WebPaymentAPI.svc/ProcessCredit/';
            $this->achEndpoint = 'https://uat.mmpay.evertecinc.com/WebPaymentAPI/WebPaymentAPI.svc/ProcessACH/';
            // Set credentials
            $this->username = "CERT4549444000033";
            $this->password = "5B034VrA";
        } else {
            // Set credentials
            $this->username = school_config('services.evertec.username');
            $this->password = school_config('services.evertec.password');
            $this->creditCardEndpoint = 'https://mmpay.evertecinc.com/WebPaymentAPI/WebPaymentAPI.svc/ProcessCredit/';
            $this->achEndpoint = 'https://mmpay.evertecinc.com/WebPaymentAPI/WebPaymentAPI.svc/ProcessACH/';
        }
    }

    /**
     * Process a credit card payment
     * 
     * @param array{
     * customerName:string,
     * customerEmail:string,
     * address1?:string,
     * address2?:string,
     * city?:string,
     * state?:string,
     * zipcode?:string,
     * trxID?:string,
     * refNumber?:string,
     * trxDescription:string,
     * trxAmount:float,
     * cardNumber:string,
     * expDate:string,
     * cvv:string,
     * trxTipAmount?:float,
     * trxTax1?:float,
     * trxTax2?:float,
     * filler1?:string,
     * filler2?:string,
     * filler3?:string} $data Payment data
     * 
     * @return array{
     * success:false,
     * error:string}|array{
     * success:false,
     * rCode:string,
     * rMsg:string}|array{
     * success:true,
     * authNumber:string,
     * merchantid:string,
     * postingdate:string,
     * rCode:string,
     * rMsg:string,
     * refNumber:string,
     * requestID:string,
     * success:true,
     * systemTrace:string,
     * trxDatetime:string,
     * trxID:string,
     * trxoper:string,
     * trxtype:string} Response from Evertec
     */
    public function processCreditCard(array $data): array
    {

        $payload = [
            "username" => $this->username,
            "password" => $this->password,
            "trxOper" => "sale",
            "accountID" => $this->prefix ?  Session::id() . '-' . $this->prefix : Session::id(),
            "customerName" => $data['customerName'],
            "customerEmail" => $data['customerEmail'],
            "address1" => $data['address1'] ?? "",
            "address2" => $data['address2'] ?? "",
            "city" => $data['city'] ?? "",
            "state" => $data['state'] ?? "",
            "zipcode" => $data['zipcode'] ?? "",
            "trxID" => $data['trxID'] ?? self::generateTransactionId(),
            "refNumber" => $data['refNumber'] ?? "",
            "trxDescription" => substr($data['trxDescription'], 0, 50), // Limit to 50 characters
            "trxAmount" => $data['trxAmount'],
            "cardNumber" => $data['cardNumber'],
            "expDate" => $data['expDate'], // MMYY format
            "cvv" => $data['cvv'],
            "trxTipAmount" => $data['trxTipAmount'] ?? "",
            "trxTax1" => $data['trxTax1'] ?? "",
            "trxTax2" => $data['trxTax2'] ?? "",
            "filler1" => $data['filler1'] ?? "",
            "filler2" => $data['filler2'] ?? "",
            "filler3" => $data['filler3'] ?? "",
        ];

        return $this->sendRequest($this->creditCardEndpoint, $payload);
    }

    /**
     * Process an ACH payment
     * 
     * @param array{
     * customerName:string,
     * customerEmail:string,
     * address1?:string,
     * address2?:string,
     * city?:string,
     * state?:string,
     * zipcode:string,
     * trxID?:string,
     * refNumber?:string,
     * trxDescription?:string,
     * trxAmount:float,
     * bankAccount:string,
     * routing:string,
     * accType:'w'|'s',
     * filler1?:string,
     * filler2?:string,
     * filler3?:string
     *} $data Payment data
     * 
     * @return array{
     * success:false,
     * error:string}|array{
     * success:false,
     * rCode:string,
     * rMsg:string}|array{
     * success:true,
     * authNumber:string,
     * merchantid:string,
     * postingdate:string,
     * rCode:string,
     * rMsg:string,
     * refNumber:string,
     * requestID:string,
     * success:true,
     * systemTrace:string,
     * trxDatetime:string,
     * trxID:string,
     * trxoper:string,
     * trxtype:string} Response from Evertec
     * */
    public function processACH($data): array
    {

        $payload = [
            "username" => $this->username,
            "password" => $this->password,
            "trxOper" => "sale",
            "accountID" => $this->prefix ?  Session::id() . '-' . $this->prefix : Session::id(),
            "customerName" => $data['customerName'],
            "customerEmail" => $data['customerEmail'],
            "address1" => $data['address1'] ?? "",
            "address2" => $data['address2'] ?? "",
            "city" => $data['city'] ?? "",
            "state" => $data['state'] ?? "",
            "zipcode" => $data['zipcode'],
            "trxID" => $data['trxID'] ?? self::generateTransactionId(),
            "refNumber" => $data['refNumber'] ?? "",
            "trxDescription" => $data['trxDescription'] ?? "",
            "trxAmount" => $data['trxAmount'],
            "bankAccount" => $data['bankAccount'],
            "routing" => $data['routing'],
            "accType" => $data['accType'] ?? "w", // Default to checking account
            "filler1" => $data['filler1'] ?? "",
            "filler2" => $data['filler2'] ?? "",
            "filler3" => $data['filler3'] ?? "",
        ];

        return $this->sendRequest($this->achEndpoint, $payload);
    }

    /**
     * Send HTTP request to Evertec API
     * 
     * @param string $endpoint API endpoint
     * @param array $payload Request payload
     * @return array{success:false,error:string}|array{success:false,rCode:string,rMsg:string} Response from API
     */
    private function sendRequest($endpoint, $payload)
    {
        $ch = curl_init($endpoint);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($error) {
            return [
                'success' => false,
                'error' => $error
            ];
        }

        $responseData = json_decode($response, true);

        // Check if it's a successful response
        if (isset($responseData['rCode'])) {
            $isSuccess = $responseData['rCode'] === '00' || $responseData['rCode'] === '0000';
            $responseData['success'] = $isSuccess;
        } else {
            $responseData['success'] = false;
        }

        return $responseData;
    }

    /**
     * Generate a unique transaction ID
     * 
     * @return string Transaction ID
     */
    public static function generateTransactionId()
    {
        return uniqid() . rand(1000, 9999);
    }
}
