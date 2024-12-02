<?php
namespace ApnaPayment\Settlements;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class HttpClient
{
    private $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => config('settlement-sdk.base_url'),
            'headers' => [
                'Authorization' => 'Bearer ' . config('settlement-sdk.api_key'),
                'Accept' => 'application/json',
            ],
        ]);
    }

    public function request($method, $uri, $options = [])
    {
        try {
            $response = $this->client->request($method, $uri, $options);
            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            $response = $e->getResponse();
            $message = $response ? $response->getBody()->getContents() : $e->getMessage();
            throw new \Exception($message, $e->getCode());
        }
    }
}
