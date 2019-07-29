<?php

namespace App\ExternalApis;

/**
 * Class CircleciAPI
 * @package App\ExternalApis
 *
 *
 * CIRCLE_API_TOKEN
 * CIRCLE_BUILD_NUM
 * CIRCLE_PROJECT_USERNAME
 * CIRCLE_PROJECT_REPONAME
 * CIRCLE_WORKFLOW_ID
 */
class CircleciAPI
{
    private $token;

    private $username;

    private $project;

    public function __construct($token)
    {
        $this->username = env('CIRCLE_USERNAME');
        $this->project = env('CIRCLE_REPONAME');

        $this->token = $token;
    }

    /**
     * Endpoint /project/:vcs-type/:username/:project/:build_num
     */
    public function getJob($jobId)
    {
        return $this->doRequest('/project/github/' . $this->username . '/' . $this->project . '/' . (int) $jobId);
    }

    private function doRequest($url)
    {
        $curlHandle = curl_init();

        $url = 'https://circleci.com/api/v1.1' . $url;

        $headers = [
            'Accept:application/json',
            'Authorization:'.$this->token,
        ];
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curlHandle, CURLOPT_URL, $url);

        $response = curl_exec($curlHandle);
        curl_close($curlHandle);

        $result = json_decode($response, true);

        return $result;
    }
}
