<?php

namespace App\Services\EkPay;

interface EkPayInterface
{
    public function setParams(array $data);

    public function merchantInformation(array $payload);

    public function customerInformation(array $payload);

    public function transactionInformation(array $payload);

    public function ipnInformation(array $payload);

    public function callToApi(array $payload, array $header = [], bool $setLocalhost = false);

    public function buildPayload(array &$data);

}
