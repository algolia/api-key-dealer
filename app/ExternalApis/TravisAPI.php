<?php

namespace App\ExternalApis;

class TravisAPI
{
    private $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function getJob($jobId)
    {
        return $this->doRequest('/jobs/'.(int) $jobId);
    }

    private function doRequest($url)
    {
        $curlHandle = curl_init();

        $url = 'https://api.travis-ci.org'.$url;

        $headers = [
            'Accept:application/vnd.travis-ci.2+json',
            'Content-Type:application/json',
            'Authorization:token '.$this->token,
        ];
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($curlHandle, CURLOPT_URL, $url);

        curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($curlHandle, CURLOPT_HTTPGET, true);
        curl_setopt($curlHandle, CURLOPT_POST, false);

        //Return the output instead of printing it
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandle, CURLOPT_FAILONERROR, true);
        curl_setopt($curlHandle, CURLOPT_ENCODING, '');
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2);

        $response = curl_exec($curlHandle);
        curl_close($curlHandle);

        $result = json_decode($response, true);

        return $result;
    }
}
