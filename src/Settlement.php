<?php
// packages/SettlementSdk/src/Settlement.php
namespace ApnaPayment\Settlements;

class Settlement
{
    protected $apiClient;

    public function __construct()
    {
        $this->apiClient = new ApiClient();
    }

    public function createSettlement($sdkUserId, $settlementAccountId, $amount)
    {
        $response = $this->apiClient->post('/api/settlements', [
            'sdk_user_id' => $sdkUserId,
            'settlement_account_id' => $settlementAccountId,
            'amount' => $amount,
        ]);
        return $response;
    }
}
