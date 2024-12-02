<?php
namespace ApnaPayment\Settlements;

use ApnaPayment\Settlements\Builders\SettlementAccountBuilder;
use ApnaPayment\Settlements\Builders\SettlementBuilder;

class Settlement
{
    protected $apiToken;
    protected $apiUrl;
    protected $data;

    public function __construct($apiToken)
    {
        $this->apiToken = $apiToken;
        $this->apiUrl = config('settlement-sdk.base_url');
    }

    /**
     * Static method to create a new settlement account
     * @param  SettlementAccountBuilder $builder
     * @return mixed
     */
    public static function createAccount(SettlementAccountBuilder $builder)
    {
        $instance = new self(config('settlement-sdk.api_token'));
        return $instance->createSettlementAccount($builder);
    }

    /**
     * Static method to create a new settlement
     * @param  SettlementBuilder $builder
     * @return mixed
     */
    public static function createNewSettlement(SettlementBuilder $builder)
    {
        $instance = new self(config('settlement-sdk.api_token'));
        return $instance->createSettlement($builder);
    }

    /**
     * Find a settlement by ID
     * @param  string $settlementId
     * @return mixed
     */
    public static function find($settlementId)
    {
        $instance = new self(config('settlement-sdk.api_token'));
        return $instance->getSettlementById($settlementId);
    }

    /**
     * Create a new settlement
     * @param  SettlementBuilder $builder
     * @return mixed
     */
    public function createSettlement(SettlementBuilder $builder)
    {
        $settlementData = $builder->build();
        $response = $this->sendRequest('POST', '/settlements', $settlementData);
        return $response;
    }

    /**
     * Create a new settlement account
     * @param  SettlementAccountBuilder $builder
     * @return mixed
     */
    public function createSettlementAccount(SettlementAccountBuilder $builder)
    {
        $settlementAccountData = $builder->build();
        $response = $this->sendRequest('POST', '/settlement-accounts', $settlementAccountData);
        return $response;
    }

    /**
     * Get settlement by ID
     * @param  string  $settlementId
     * @return mixed
     */
    public function getSettlementById($settlementId)
    {
        $response = $this->sendRequest('GET', "/settlements/{$settlementId}");
        $this->data = $response;  // Save response for status checks
        return $response;
    }

    /**
     * Get all settlements for the user
     * @param  array  $filters
     * @return mixed
     */
    public function getAllSettlements(array $filters = [])
    {
        $response = $this->sendRequest('GET', '/settlements', $filters);
        return $response;
    }

    /**
     * Get all settlements for a specific settlement account
     * @param  string  $settlementAccountId
     * @return mixed
     */
    public function getSettlementsByAccount($settlementAccountId)
    {
        $response = $this->sendRequest('GET', '/settlements', ['settlement_account_id' => $settlementAccountId]);
        return $response;
    }

    /**
     * Get balance for the current user
     * @return mixed
     */
    public function getBalance()
    {
        $response = $this->sendRequest('GET', '/balance');
        return $response;
    }

    /**
     * Helper function to check if settlement is pending
     * @return bool
     */
    public function isPending()
    {
        return $this->data['status'] === 'pending';
    }

    /**
     * Helper function to check if settlement is processing
     * @return bool
     */
    public function isProcessing()
    {
        return $this->data['status'] === 'processing';
    }

    /**
     * Helper function to check if settlement is completed
     * @return bool
     */
    public function isCompleted()
    {
        return $this->data['status'] === 'completed';
    }

    /**
     * Helper function to check if settlement is failed
     * @return bool
     */
    public function isFailed()
    {
        return $this->data['status'] === 'failed';
    }

    /**
     * Send API request to the server
     * @param  string  $method
     * @param  string  $endpoint
     * @param  array   $data
     * @return mixed
     */
    private function sendRequest($method, $endpoint, array $data = [])
    {
        $client = new \GuzzleHttp\Client();

        // Adding the API token to headers
        $headers = [
            'Authorization' => 'Bearer ' . $this->apiToken,
        ];

        // Set data for POST/PUT requests
        $options = [
            'headers' => $headers,
            'json' => $data
        ];

        if ($method === 'GET') {
            unset($options['json']);
            $options['query'] = $data;
        }

        $response = $client->request($method, $this->apiUrl . $endpoint, $options);

        return json_decode($response->getBody(), true);
    }
}
