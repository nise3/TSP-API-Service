<?php

namespace App\Services\EkPay;

use phpseclib3\Common\Functions\Strings;

abstract class EkPayAbstractionService implements EkPayInterface
{
    protected string $apiUrl;
    protected string $merchantId;
    protected string $merchantPassword;
    protected string $merchantMacAddress;
    protected string $requestTimestamp;
    protected array $errors = [];
    protected array $requestPayload = [];

    /**
     * @return string
     */
    public function getApiUrl(): string
    {
        return $this->apiUrl;
    }

    /**
     * @param string $apiUrl
     */
    public function setApiUrl(string $apiUrl): void
    {
        $this->apiUrl = $apiUrl;
    }

    /**
     * @return string
     */
    public function getMerchantId(): string
    {
        return $this->merchantId;
    }

    /**
     * @param string $merchantId
     */
    public function setMerchantId(string $merchantId): void
    {
        $this->merchantId = $merchantId;
    }

    /**
     * @return string
     */
    public function getMerchantPassword(): string
    {
        return $this->merchantPassword;
    }

    /**
     * @param string $merchantPassword
     */
    public function setMerchantPassword(string $merchantPassword): void
    {
        $this->merchantPassword = $merchantPassword;
    }

    /**
     * @return string
     */
    public function getMerchantMacAddress(): string
    {
        return $this->merchantMacAddress;
    }

    /**
     * @param string $merchantMacAddress
     */
    public function setMerchantMacAddress(string $merchantMacAddress): void
    {
        $this->merchantMacAddress = $merchantMacAddress;
    }

    /**
     * @return string
     */
    public function getRequestTimestamp(): string
    {
        return $this->requestTimestamp;
    }

    /**
     * @param string $requestTimestamp
     */
    public function setRequestTimestamp(string $requestTimestamp): void
    {
        $this->requestTimestamp = $requestTimestamp;
    }


    public function callToApi(array $payload, array $header = [], bool $setLocalhost = false)
    {
        // TODO: Implement callToApi() method.
    }

    public function setParams(array $data)
    {
        $this->merchantInformation($data);
        $this->customerInformation($data);
        $this->transactionInformation($data);
    }

    public function merchantInformation(array $payload)
    {
        if (!$this->getMerchantId()) {
            $this->errors = [
                "status" => false,
                "message" => "Merchant Id is InValid"
            ];
        } elseif (!$this->getMerchantPassword()) {
            $this->errors = [
                "status" => false,
                "message" => "Merchant Password is InValid"
            ];
        }

        $this->requestPayload['mer_info']['mer_reg_id'] = $this->getMerchantId();
        $this->requestPayload['mer_info']['mer_pas_key'] = $this->getMerchantPassword();

    }

    public function customerInformation(array $payload)
    {

        if (empty($payload['cust_info']['cust_id'])) {
            $this->errors = [
                "status" => false,
                "message" => "Customer id is InValid"
            ];
        } elseif (empty($payload['cust_info']['cust_name'])) {
            $this->errors = [
                "status" => false,
                "message" => "Customer name is InValid"
            ];
        } elseif (empty($payload['cust_info']['cust_mobo_no'])) {
            $this->errors = [
                "status" => false,
                "message" => "Customer mobile is InValid"
            ];
        } elseif (empty($payload['cust_info']['cust_email'])) {
            $this->errors = [
                "status" => false,
                "message" => "Customer mail is InValid"
            ];
        }
        $this->requestPayload['cust_info'] = $payload['cust_info'];

    }

    public function transactionInformation(array $payload)
    {

        if (empty($payload['trns_info']['trnx_id'])) {
            $this->errors = [
                "status" => false,
                "message" => "Transaction id id is InValid"
            ];
        } elseif (empty($payload['trns_info']['trnx_amt'])) {
            $this->errors = [
                "status" => false,
                "message" => "Transaction amount is InValid"
            ];
        } elseif (empty($payload['trns_info']['trnx_currency'])) {
            $this->errors = [
                "status" => false,
                "message" => "Transaction currency is InValid"
            ];
        } elseif (empty($payload['trns_info']['ord_id'])) {
            $this->errors = [
                "status" => false,
                "message" => "Transaction order id is InValid"
            ];
        }
        $this->requestPayload['trns_info'] = $payload['trns_info'];

    }

}
