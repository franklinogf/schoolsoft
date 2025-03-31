<?php

namespace Classes\Services;

use Classes\Session;

class EvertecPayment
{
    private $creditCardEndpoint;
    private $achEndpoint;
    private $username;
    private $password;
    private $prefix;
    private $isDevelopment;

    public function __construct($isDevelopment = true, $prefix = null)
    {
        $this->prefix = $prefix;
        $this->isDevelopment = $isDevelopment;

        // Set endpoints based on environment
        if ($isDevelopment) {
            $this->creditCardEndpoint = 'https://uat.mmpay.evertecinc.com/WebPaymentAPI/WebPaymentAPI.svc/ProcessCredit/';
            $this->achEndpoint = 'https://uat.mmpay.evertecinc.com/WebPaymentAPI/WebPaymentAPI.svc/ProcessACH/';
            // Set credentials
            $this->username = "CERT4549444000033";
            $this->password = "5B034VrA";
        } else {
            // Set credentials
            $this->username = "ECOM4549555000561";
            $this->password = "h1MT6Eh24WDQ8LNJ";
            $this->creditCardEndpoint = 'https://mmpay.evertecinc.com/WebPaymentAPI/WebPaymentAPI.svc/ProcessCredit/';
            $this->achEndpoint = 'https://mmpay.evertecinc.com/WebPaymentAPI/WebPaymentAPI.svc/ProcessACH/';
        }
    }

    /**
     * Process a credit card payment
     * 
     * @param array $data Payment data
     * 
     * @param string $data['customerName'] Customer's name
     * @param string $data['customerEmail'] Customer's email
     * @param string $data['address1'] Customer's address line 1
     * @param string $data['address2'] Customer's address line 2
     * @param string $data['city'] Customer's city
     * @param string $data['state'] Customer's state
     * @param string $data['zipcode'] Customer's zipcode
     * @param string $data['trxID'] Transaction ID
     * @param string $data['refNumber'] Reference number
     * @param string $data['trxDescription'] Transaction description
     * @param float $data['trxAmount'] Transaction amount
     * @param string $data['cardNumber'] Credit card number
     * @param string $data['expDate'] Expiration date (MMYY format)
     * @param string $data['cvv'] CVV code
     * @param float $data['trxTipAmount'] Tip amount
     * @param float $data['trxTax1'] Tax 1 amount
     * @param float $data['trxTax2'] Tax 2 amount
     * @param string $data['filler1'] Filler 1
     * @param string $data['filler2'] Filler 2
     * @param string $data['filler3'] Filler 3
     * 
     * @return array Response from Evertec
     */
    public function processCreditCard(array $data)
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
            "trxID" => $data['trxID'],
            "refNumber" => $data['refNumber'] ?? "",
            "trxDescription" => $data['trxDescription'],
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
     * @param array $data Payment data
     * 
     * @param string $data['customerName'] Customer's name
     * @param string $data['customerEmail'] Customer's email
     * @param string $data['address1'] Customer's address line 1
     * @param string $data['address2'] Customer's address line 2
     * @param string $data['city'] Customer's city
     * @param string $data['state'] Customer's state
     * @param string $data['zipcode'] Customer's zipcode
     * @param string $data['trxID'] Transaction ID
     * @param string $data['refNumber'] Reference number
     * @param string $data['trxDescription'] Transaction description
     * @param float $data['trxAmount'] Transaction amount
     * @param string $data['bankAccount'] Bank account number
     * @param string $data['routing'] Routing number
     * @param string $data['accType'] Account type W|S|C (default to checking account)     
     * @param string $data['filler1'] Filler 1
     * @param string $data['filler2'] Filler 2
     * @param string $data['filler3'] Filler 3
     * 
     * @return array Response from Evertec
     */
    public function processACH($data)
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
            "zipcode" => $data['zipcode'] ?? "00960",
            "trxID" => $data['trxID'],
            "refNumber" => $data['refNumber'] ?? "",
            "trxDescription" => $data['trxDescription'],
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
     * @return array Response from API
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
