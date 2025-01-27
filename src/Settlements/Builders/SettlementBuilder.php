<?php
namespace ApnaPayment\Settlements\Builders;

class SettlementBuilder
{
    private $amount;
    private $remarks;
    private $settlementAccountId;
    private $txnId;

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    public function setRemarks(string $remarks): self
    {
        $this->remarks = $remarks;
        return $this;
    }

    public function setTxnId(string $txnId): self
    {
        $this->txnId = $txnId;
        return $this;
    }
    public function getTxnId(): string|null
    {
        return $this->data["txnId"]??null;
    }
    public function setSettlementAccountId(string $settlementAccountId): self
    {
        $this->settlementAccountId = $settlementAccountId;
        return $this;
    }

    public function build(): array
    {
        if (!$this->amount || !$this->settlementAccountId) {
            throw new \InvalidArgumentException('Required fields are missing for settlement');
        }

        return [
            'amount' => $this->amount,
            'remarks' => $this->remarks,
            'settlement_account_id' => $this->settlementAccountId,
            'txnId'=>$this->txnId
        ];
    }
}
