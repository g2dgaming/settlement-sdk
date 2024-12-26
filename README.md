# ApnaPayment Settlement SDK

## Overview
This PHP SDK allows you to integrate ApnaPayment's settlement system into your application. You can use it to create settlement accounts, create settlements, check settlement statuses, and more.

## Installation

To use the ApnaPayment Settlement SDK, first install it via Composer:

composer require apna-payment/settlement-sdk

### Configuration

After installation, publish the configuration file:

    php artisan vendor:publish --provider="ApnaPayment\Settlements\SettlementServiceProvider" --tag="config"

This will publish a `settlement-sdk.php` configuration file to the `config` folder of your Laravel project.

In your `.env` file, set your API token:

SETTLEMENT_BASE_URL=<base_url_depending_on_env>

SETTLEMENT_API_TOKEN=<your_api_token_here>

## Usage

#### Create a VPA-based settlement account
    try{
        $account = (new SettlementAccountBuilder())
        ->setAccountHolderName("My Name")
        ->setType(SettlementAccountBuilder::$TYPE_VPA)
        ->setVirtualAddress("testvpa@hdfcbank")
        ->setNickname("My Temp account test");
        $accountId = Settlement::createAccount($account);
    }
    catch (\ApnaPayment\Settlements\Exceptions\DuplicationAccountException $e){
        //Account already exists
    }

#### Create a bank account-based settlement account
    
    try{
        $account = (new SettlementAccountBuilder())
        ->setType(SettlementAccountBuilder::$TYPE_BANK_ACCOUNT)
        ->setAccountNumber("988231872481874")
        ->setAccountHolderName("My Name")
        ->setIfscCode("IFSCTEST001")
        ->setNickname("My Name");
        $accountId = Settlement::createAccount($account);
    }
    catch (\ApnaPayment\Settlements\Exceptions\DuplicationAccountException $e){
        //Account already exists
    }

#### Delete a settlement account 
    $is_deleted=Settlement::removeAccount($accountId); //returns boolean
    if($is_deleted){
        //Account deleted successfully
    }
    else{
    //Something went wrong
    }
#### Fetch all settlements
    $accounts = Settlement::getAllSettlements();

#### Fetch settlements for specific accounts
    $accounts = Settlement::getSettlementsByAccount($accountId);

#### Handle an invalid account ID
    try {
        $settlements = Settlement::getSettlementsByAccount("InvalidId");
    } 
    catch (\ApnaPayment\Settlements\Exceptions\InvalidAccountException) {
        $message = "Invalid Account";
    }

#### Create settlement
    $settlementBuilder = (new SettlementBuilder())
    ->setAmount(200.25)
    ->setRemarks("Test Payment")
    ->setSettlementAccountId($accountId);
    $settlement = Settlement::createNewSettlement($settlementBuilder);


## Exception Handling
    try {
        $settlement = (new Settlement(config('settlement-sdk.api_token')))
    ->createSettlement($settlementBuilder);
    } 
    catch (\ApnaPayment\Settlements\Exceptions\DuplicateTransactionException $e) {
        $message = "Duplicate";
    } 
    catch (\ApnaPayment\Settlements\Exceptions\InvalidAccountException $e) {
        $message = "Invalid account";
    } catch (\ApnaPayment\Settlements\Exceptions\LimitExceededException $e) {
        $message = "Limit exceeded";
    }

## Fetch account balance
    $balance = Settlement::getBalance();

## Find a settlement by ID
    $settlement = Settlement::find($settlementId); //settlement id is always returned from creating a settlement (static or from object)

## Usage of Webhook Base URL:
### Callbacks V1
#### Note:
###### All Endpoints when triggered will expect 200-299 http response code, even if in the case 500,400 is thrown, there will always be one time updates to balance server load.

After our backend receives a valid web hook url for updates relating to following events:
1. Settlement status: {'completed','failed'}
2. Account Approval Update : { 'approved','rejected' }

### Our Backend triggers an HTTP/HTTPS POST request to following endpoints: 
#####     1. {base_url}/settlement/status :  
        {
            'id':  '<settlement_id>',
            'status':'completed'
        }
#####     2. {base_url}/settlement/status :
        {
            'id':  '<settlement_id>',
            ‘status’:’failed’
        }
##### 3. {base_url}/settlement_account/approval :  
        { 
            'id':  '<settlement_account_id>',
            'status':'rejected'
        }
##### 4.{base_url}/settlement_account/approval :
        {
            'id':  '<settlement_account_id>',
            'status':'approved'
        }


## Querying the status of a settlement
#### Suppose you have the settlementId returned from Settlement::createSettlemen($settlementBuilder)
    $settlement = new Settlement();
    // Load the settlement data

    $settlement->getSettlementById($settlementId)

    // Now the 'data' attribute has status in it

    if($settlement->isPending()){
        // Pending
    }
    else if($settlement->isProcessing()){
        // Processing
    }
    else if($settlement->isCompleted()){
        // Completed
    }
    else if( $settlement->isFailed()){
        // Failed 
    }




## Methods Overview

- **createAccount(SettlementAccountBuilder $builder)**: Creates a new settlement account using the provided builder.
- **createNewSettlement(SettlementBuilder $builder)**: Creates a new settlement using the provided builder.
- **allSettlements()**: Retrieves all settlements for the authenticated user.
- **settlementsByAccount(string $accountId)**: Retrieves settlements for a specific settlement account.
- **find(string $settlementId)**: Finds a specific settlement by its ID.
- **checkBalance()**: Retrieves the balance associated with the authenticated user.
- **isPending()**: Checks if the settlement is in the 'pending' state.
- **isProcessing()**: Checks if the settlement is in the 'processing' state.
- **isCompleted()**: Checks if the settlement is in the 'completed' state.
- **isFailed()**: Checks if the settlement has 'failed' status.

---
