<?php
class REDFBrokerageAPI {
    private $appKey;
    private $password;
    private $baseUrl;
    private $token;

    public function __construct() {
        $this->appKey   = 'pvi403';
        $this->password = 'ygt002260';
        $this->baseUrl  = 'https://dlp-uat-fe-gw.redf.gov.sa:801/applead';
    }

    private function request($method, $endpoint, $body , $isAuthRequired = false, $token=null) {
        $url = $this->baseUrl . $endpoint;
        $headers = [];
        $headers[] = 'Content-Type: application/json';
    
        if ($isAuthRequired === true && !empty($token)) {
            $headers[] = 'token: ' . $token;
        }
    
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $endpoint,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_POSTFIELDS => $body,
        CURLOPT_HTTPHEADER => $headers,
        ));
      
      	//var_dump($endpoint);
      	//var_dump($method);
      	//var_dump($body);
      	//var_dump($headers);
        
        $response = curl_exec($curl);
        $responseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        return $response;
    }
    

    public function getToken() {
        $body = '{
                    "key":"pvi403",
                    "password":"ygtO02260"
                }';

        $result = $this->request('POST', 'https://dlp-uat-fe-gw.redf.gov.sa:801/applead/api/Brokerage/get-app-token', $body, false);
      	
        return $result;
    }

    public function createProperty($propertyData) {
        $result = $this->request('POST', 'https://dlp-uat-fe-gw.redf.gov.sa:801/listData/api/external/properties', $propertyData, true, $this->getToken());
        return $result;
    }

    public function updateProperty($uid, $propertyData) {
        return $this->request('PATCH', "/api/external/properties/{$uid}", $propertyData, true, $this->getToken());
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