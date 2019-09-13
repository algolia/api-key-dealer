<?php

namespace App\ExternalApis;

class CircleciAPI
{
    private $token;

    private $username;

    private $project;

    public function __construct($token, $user, $repo)
    {
        $this->token = $token;
        $this->username = $user;
        $this->project = $repo;
    }

    public function getJob($jobId)
    {
        return $this->doRequest('/project/github/' . $this->username . '/' . $this->project . '/' . (int) $jobId);
    }

    private function doRequest($url)
    {
        $curlHandle = curl_init();

        $url = 'https://circleci.com/api/v1.1' . $url . '?circle-token=' . $this->token;

        $headers = [
            'Accept:application/json',
        ];

        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curlHandle, CURLOPT_URL, $url);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curlHandle);
        curl_close($curlHandle);

        $result = json_decode($response, true);

        return $result;
    }
}
