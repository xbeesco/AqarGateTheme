<?php
class REDFBrokerageAPI {
    private $appKey;
    private $password;
    private $baseUrl;
    private $token;

    public function __construct() {
        $this->appKey   = 'pvi03';
        $this->password = 'ygt002260';
        $this->baseUrl  = 'https://dlp-uat-fe-gw.redf.gov.sa:801/listingsdata-api';
    }

    private function request($method, $endpoint, $body = null, $isAuthRequired = true) {
        $url = $this->baseUrl . $endpoint;
        $headers = ['Content-Type' => 'application/json'];

        if ($isAuthRequired && $this->token) {
            $headers['Authorization'] = 'Bearer ' . $this->token;
        }

        $args = [
            'method' => $method,
            'headers' => $headers,
            'sslverify' => false,  // Add this line to bypass SSL verification
        ];

        if ($body) {
            $args['body'] = json_encode($body);
        }

        $response = wp_remote_request($url, $args);

        if (is_wp_error($response)) {
            throw new Exception($response->get_error_message());
        }

        $responseBody = wp_remote_retrieve_body($response);
        $responseCode = wp_remote_retrieve_response_code($response);

        return ['code' => $responseCode, 'response' => json_decode($responseBody, true)];
    }

    public function getToken() {
        $body = [
            'AppKey' => $this->appKey,
            'Password' => $this->password,
        ];

        $result = $this->request('POST', '/api/Brokerage/get-app-token', $body, false);
        if ($result['code'] == 200 && isset($result['response']['token'])) {
            $this->token = $result['response']['token'];
        } else {
            return $result;
        }
    }

    public function createProperty($propertyData) {
        return $this->request('POST', '/api/external/properties', $propertyData);
    }

    public function updateProperty($uid, $propertyData) {
        return $this->request('PATCH', "/api/external/properties/{$uid}", $propertyData);
    }

    public function searchProperty($uid) {
        return $this->request('GET', "/api/external/properties/{$uid}", null, false);
    }

    public function createProject($projectData) {
        return $this->request('POST', '/api/external/projects', $projectData);
    }

    public function updateProject($uid, $projectData) {
        return $this->request('PATCH', "/api/external/projects/{$uid}", $projectData);
    }

    public function searchProject($uid) {
        return $this->request('GET', "/api/external/projects/{$uid}", null, false);
    }
}