<?php
namespace ApnaPayment\Settlements;

use GuzzleHttp\Client;

class ApiClient
{
    protected $client;
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = config('sdk.api_key');
        $this->baseUrl = config('sdk.api_url');
    }

    public function get($uri, $params = [])
    {
        return $this->request('GET', $uri, $params);
    }

    public function post($uri, $params = [])
    {
        return $this->request('POST', $uri, $params);
    }

    protected function request($method, $uri, $params = [])
    {
        try {
            $response = $this->client->request($method, $this->baseUrl . $uri, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ],
                'json' => $params,
            ]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            throw new \Exception("API Request failed: " . $e->getMessage());
        }
    }
}
