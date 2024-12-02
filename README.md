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

SETTLEMENT_API_TOKEN=your_api_token_here

## Usage

### 1. Create a New Settlement Account

To create a new settlement account (either `vpa` or `bank_account`), you can use the `createAccount` method. You need to create a builder object to set the account's details.

Example:

use ApnaPayment\Settlements\SettlementAccountBuilder;

// Create a new settlement account $accountBuilder = new SettlementAccountBuilder(); $accountBuilder->setType('vpa') // or 'bank_account' ->setNickname('MyAccount') ->setVirtualAddress('vpa@domain.com'); // or set account details for 'bank_account'

// Create the account $account = \ApnaPayment\Settlements\Settlement::createAccount($accountBuilder);

// Response: The account object will contain information about the created settlement account



---

### 2. Create a New Settlement

To create a new settlement, you need to use the `createNewSettlement` method with a `SettlementBuilder` object to specify the settlement's details.

Example:

use ApnaPayment\Settlements\SettlementBuilder;

// Create a new settlement $settlementBuilder = new SettlementBuilder(); $settlementBuilder->setAmount(1000) ->setSettlementAccountId('account_id_here') // The ID of the settlement account ->setRemarks('Settlement for vendor payment');

// Create the settlement $settlement = \ApnaPayment\Settlements\Settlement::createNewSettlement($settlementBuilder);

// Response: The settlement object will contain information about the created settlement.


---

### 3. Get All Settlements

To retrieve all settlements associated with the authenticated user, you can call the `allSettlements` method.

Example:

$settlements = \ApnaPayment\Settlements\Settlement::allSettlements();

// Response: An array of settlement objects.

You can also filter settlements by settlement account ID using the `settlementsByAccount` method.

Example:
$settlements = \ApnaPayment\Settlements\Settlement::settlementsByAccount('account_id_here');

// Response: An array of settlements filtered by the specified account ID.

---

### 4. Check Settlement Status

To check the status of a specific settlement, you can use the `find` method followed by status-checking methods such as `isPending()`, `isProcessing()`, `isCompleted()`, and `isFailed()`.

Example:

// Find a settlement by ID $settlement = \ApnaPayment\Settlements\Settlement::find('settlement_id_here');

// Check the status of the settlement if ($settlement->isPending()) { echo "Settlement is pending."; }

if ($settlement->isCompleted()) { echo "Settlement is completed."; }

if ($settlement->isFailed()) { echo "Settlement failed."; }

---

### 5. Check Balance

To check the balance of a settlement user, you can use the `checkBalance` method.

Example:

$balance = \ApnaPayment\Settlements\Settlement::checkBalance();

// Response: The balance amount associated with the authenticated user.


---

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
