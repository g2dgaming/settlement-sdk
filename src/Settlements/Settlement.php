<?php
namespace ApnaPayment\Settlements;

use ApnaPayment\Settlements\Builders\SettlementAccountBuilder;
use ApnaPayment\Settlements\Builders\SettlementBuilder;
use ApnaPayment\Settlements\Exceptions\DailyLimitExceededException;
use ApnaPayment\Settlements\Exceptions\DuplicateTransactionException;
use ApnaPayment\Settlements\Exceptions\InsufficientAccountBalanceException;
use ApnaPayment\Settlements\Exceptions\InvalidAccountException;
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
     * @throws DailyLimitExceededException,DuplicateTransactionException,InsufficientAccountBalanceException,InvalidAccountException,UnauthorizedAccessException
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
     *
     */
    public function createSettlementAccount(SettlementAccountBuilder $builder)
    {
        $settlementAccountData = $builder->build();
        $response = $this->sendRequest('POST', '/settlements/account', $settlementAccountData);
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
    public static function getAllSettlements(array $filters = [])
    {
        $instance = new self(config('settlement-sdk.api_token'));
        $response = $instance->sendRequest('GET', '/settlements', $filters);
        return $response;
    }

    /**
     * Get all settlements for a specific settlement account
     * @param  string  $settlementAccountId
     * @return mixed
     * @throws InvalidAccountException
     */
    public static function getSettlementsByAccount($settlementAccountId)
    {
        $instance = new self(config('settlement-sdk.api_token'));
        $response = $instance->sendRequest('GET', '/settlements/account/'.$settlementAccountId,);
        return $response;
    }

    /**
     * Get balance for the current user
     * @return mixed
     */
    public static function getBalance()
    {
        $instance = new self(config('settlement-sdk.api_token'));
        $response = $instance->sendRequest('GET', '/balance');
        return $response['balance'];
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
            if($e->getCode() == 402){
                throw new InsufficientAccountBalanceException();
            }
            else if($e->getCode() == 409){
                throw new DuplicateTransactionException();
            }
            else if($e->getCode() == 403){
                throw new DailyLimitExceededException();
            }
            else if($e->getCode() == 402){
                throw new UnauthorizedAccessException();
            }
            else if ($e->getCode() == 400){
                throw new InvalidAccountException();
            }
        }


    }
}
