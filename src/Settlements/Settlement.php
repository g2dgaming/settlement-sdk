<?php
namespace ApnaPayment\Settlements;

use ApnaPayment\Settlements\Builders\SettlementAccountBuilder;
use ApnaPayment\Settlements\Builders\SettlementBuilder;
use ApnaPayment\Settlements\Exceptions\DailyLimitExceededException;
use ApnaPayment\Settlements\Exceptions\DuplicateTransactionException;
use ApnaPayment\Settlements\Exceptions\InsufficientAccountBalanceException;
use ApnaPayment\Settlements\Exceptions\InvalidAccountException;
use ApnaPayment\Settlements\Exceptions\ServerException;
use ApnaPayment\Settlements\Exceptions\UnauthorizedAccessException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

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
     * @param SettlementAccountBuilder $builder
     * @return mixed
     * @throws UnauthorizedAccessException
     * @throws InvalidAccountException
     * @throws ServerException
     */
    public static function createAccount(SettlementAccountBuilder $builder): mixed
    {
        try {
            $instance = new self(config('settlement-sdk.api_token'));
            return $instance->createSettlementAccount($builder);
        }
        catch (ServerException $e){
            if($e->getCode() == 401){
                throw new InvalidAccountException();
            }
            throw $e;
        }
    }

    /**
     * Static method to create a new settlement
     * @param  SettlementBuilder $builder
     * @return mixed
     * @throws InsufficientAccountBalanceException
     * @throws DuplicateTransactionException
     * @throws DailyLimitExceededException
     * @throws UnauthorizedAccessException
     * @throws InvalidAccountException
     * @throws ServerException
     */
    public static function createNewSettlement(SettlementBuilder $builder): mixed
    {
        $instance = new self(config('settlement-sdk.api_token'));
        return $instance->createSettlement($builder);
    }

    /**
     * Find a settlement by ID
     * @param string $settlementId
     * @return mixed
     */
    public static function find(string $settlementId): mixed
    {
        $instance = new self(config('settlement-sdk.api_token'));
        return $instance->getSettlementById($settlementId);
    }

    /**
     * Create a new settlement
     * @param SettlementBuilder $builder
     * @return mixed
     * @throws InsufficientAccountBalanceException
     * @throws DuplicateTransactionException
     * @throws DailyLimitExceededException
     * @throws UnauthorizedAccessException
     * @throws InvalidAccountException
     * @throws ServerException
     */
    public function createSettlement(SettlementBuilder $builder): mixed
    {
        try {
            $settlementData = $builder->build();
            return $this->sendRequest('POST', '/settlements', $settlementData);
        }
        catch (ServerException $e){
            if($e->getCode() == 402){
                throw new InsufficientAccountBalanceException();
            }
            else if($e->getCode() == 409){
                throw new DuplicateTransactionException();
            }
            else if($e->getCode() == 403){
                throw new DailyLimitExceededException();
            }
            else if ($e->getCode() == 400){
                throw new InvalidAccountException();
            }
            throw $e;
        }
    }

    /**
     * Create a new settlement account
     * @param SettlementAccountBuilder $builder
     * @return mixed
     * @throws UnauthorizedAccessException
     * @throws ServerException
     */
    public function createSettlementAccount(SettlementAccountBuilder $builder): mixed
    {
        try {
            $settlementAccountData = $builder->build();
            return $this->sendRequest('POST', '/settlements/account', $settlementAccountData);
        }
        catch (ServerException $e){
            if ($e->getCode() == 400){
                throw new InvalidAccountException();
            }
            throw $e;
        }
    }

    /**
     * Get settlement by ID
     * @param string $settlementId
     * @return mixed
     * @throws UnauthorizedAccessException
     * @throws ServerException
     */
    public function getSettlementById(string $settlementId): mixed
    {
        $response = $this->sendRequest('GET', "/settlements/{$settlementId}");
        $this->data = $response;  // Save response for status checks
        return $response;
    }

    /**
     * Get all settlements for the user
     * @param array $filters
     * @return mixed
     * @throws UnauthorizedAccessException
     * @throws ServerException
     */
    public static function getAllSettlements(array $filters = []): mixed
    {
        $instance = new self(config('settlement-sdk.api_token'));
        return $instance->sendRequest('GET', '/settlements', $filters);
    }

    /**
     * Get all settlements for a specific settlement account
     * @param string $settlementAccountId
     * @return mixed
     * @throws ServerException
     * @throws UnauthorizedAccessException
     */
    public static function getSettlementsByAccount(string $settlementAccountId): mixed
    {
        $instance = new self(config('settlement-sdk.api_token'));
        return $instance->sendRequest('GET', '/settlements/account/'.$settlementAccountId,);
    }

    /**
     * Get balance for the current user
     * @return mixed
     * @throws UnauthorizedAccessException|ServerException
     */
    public static function getBalance(): mixed
    {
        $instance = new self(config('settlement-sdk.api_token'));
        $response = $instance->sendRequest('GET', '/balance');
        return $response['balance'];
    }
    /**
     * Helper function to check if settlement is pending
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->data['status'] === 'pending';
    }

    /**
     * Helper function to check if settlement is processing
     * @return bool
     */
    public function isProcessing(): bool
    {
        return $this->data['status'] === 'processing';
    }

    /**
     * Helper function to check if settlement is completed
     * @return bool
     */
    public function isCompleted(): bool
    {
        return $this->data['status'] === 'completed';
    }

    /**
     * Helper function to check if settlement is failed
     * @return bool
     */
    public function isFailed(): bool
    {
        return $this->data['status'] === 'failed';
    }

    /**
     * Send API request to the server
     * @param string $method
     * @param string $endpoint
     * @param array $data
     * @return mixed
     * @throws UnauthorizedAccessException
     * @throws ServerException
     */
    private function sendRequest(string $method, string $endpoint, array $data = []): mixed
    {
        $client = new \GuzzleHttp\Client();
        // Adding the API token to headers
        $headers = [
            'Authorization' => 'Bearer ' . $this->apiToken,
            'Accept'=>'application/json'
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
        try {
            $response = $client->request($method, $this->apiUrl . $endpoint, $options);
            return json_decode($response->getBody(), true);

        }
        catch (GuzzleException $e){
            if($e->getCode() == 401){
                throw new UnauthorizedAccessException($e->getMessage(),$e->getCode());
            }
            else{
                throw new ServerException($e->getMessage(),$e->getCode());
            }
        }
    }
}

