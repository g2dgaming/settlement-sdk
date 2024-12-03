# ApnaPayment Settlement SDK

## Overview

The **ApnaPayment Settlement SDK** is a PHP library that simplifies integration with ApnaPayment's settlement system. It provides an easy-to-use interface to create settlement accounts, create settlements, fetch settlement details, and check their statuses in your application.

## Installation

To install the ApnaPayment Settlement SDK, use Composer:

composer require apna-payment/settlement-sdk

### Configuration

After installation, publish the configuration file:

php artisan vendor:publish --provider="ApnaPayment\Settlements\SettlementServiceProvider" --tag="config"

This will create a `settlement-sdk.php` configuration file in the `config` folder of your Laravel project.

Add your API token to your `.env` file:

SETTLEMENT_API_TOKEN=your_api_token_here

## Usage

Here’s how you can use the SDK in your application:

### 1. Create a VPA-based Settlement Account

$account1 = new SettlementAccountBuilder();
$account1->setType(SettlementAccountBuilder::$TYPE_VPA);
$account1->setVirtualAddress("9451526930@ybl");
$account1->setNickname("My Temp account test");
$response[1] = Settlement::createAccount($account1);

### 2. Create a Bank Account-based Settlement Account

$account2 = new SettlementAccountBuilder();
$account2->setType(SettlementAccountBuilder::$TYPE_BANK_ACCOUNT);
$account2->setAccountNumber("988231872481874");
$account2->setAccountHolderName("My Name");
$account2->setIfscCode("IFSCTEST001");
$account2->setNickname("My Name");
$response[2] = Settlement::createAccount($account2); // Returns account ID as string

### 3. Fetch All Settlements

$response[3] = Settlement::getAllSettlements();

### 4. Fetch Settlements for Specific Accounts

$response[4] = Settlement::getSettlementsByAccount($response[1]);
$response[5] = Settlement::getSettlementsByAccount($response[2]);

### 5. Handle an Invalid Account ID

try {
$response[5] = Settlement::getSettlementsByAccount("InvalidId");
} catch (\ApnaPayment\Settlements\Exceptions\InvalidAccountException) {
$response[5] = "Invalid Account";
}

### 6. Create Settlements

$settlementBuilder = new SettlementBuilder();
$settlementBuilder->setAmount(200.25);
$settlementBuilder->setRemarks("Agent APS0013 payment");
$settlementBuilder->setSettlementAccountId($response[1]);

$settlementBuilder2 = new SettlementBuilder();
$settlementBuilder2->setAmount(120.25);
$settlementBuilder2->setRemarks("Agent APS0013 payment");
$settlementBuilder2->setSettlementAccountId($response[2]);

$response[6] = Settlement::createNewSettlement($settlementBuilder);

try {
$response[7] = (new Settlement(config('settlement-sdk.api_token')))
->createSettlement($settlementBuilder2);
} catch (\ApnaPayment\Settlements\Exceptions\DuplicateTransactionException $e) {
$response[7] = "Duplicate";
} catch (\ApnaPayment\Settlements\Exceptions\InvalidAccountException $e) {
$response[7] = "Invalid account";
} catch (\ApnaPayment\Settlements\Exceptions\DailyLimitExceededException $e) {
$response[7] = "Limit exceeded";
}

### 7. Fetch Account Balance

$response[8] = Settlement::getBalance();

### 8. Find a Settlement by ID

$response[9] = Settlement::find("test id");

### 8. Get Status of Settlement directly

$response[10] = Settlement::find("id")->isPending();

### 8. Get Status of Settlement from object

$settlement = new Settlement ();
$settlement->getSettlementById("id");
$response[11]=$settlement->isPending()?"yes":"no";




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
