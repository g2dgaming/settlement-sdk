<?php
namespace ApnaPayment\Settlements\Builders;

class SettlementAccountBuilder
{
    private $nickname;
    private $accountNumber;
    private $ifscCode;
    private $accountHolderName;
    private $virtualAddress;
    private $type; // 'vpa' or 'bank_account'
    public static string $TYPE_VPA='vpa';
    public static string $TYPE_BANK_ACCOUNT='bank_account';

    public function setNickname(string $nickname): self
    {
        $this->nickname = $nickname;
        return $this;
    }

    public function setAccountNumber(string $accountNumber): self
    {
        $this->accountNumber = $accountNumber;
        return $this;
    }

    public function setIfscCode(string $ifscCode): self
    {
        $this->ifscCode = $ifscCode;
        return $this;
    }

    public function setAccountHolderName(string $accountHolderName): self
    {
        $this->accountHolderName = $accountHolderName;
        return $this;
    }

    public function setVirtualAddress(string $virtualAddress): self
    {
        $this->virtualAddress = $virtualAddress;
        return $this;
    }

    public function setType(string $type): self
    {
        if (!in_array($type, ['vpa', 'bank_account'])) {
            throw new \InvalidArgumentException('Invalid account type');
        }

        $this->type = $type;
        return $this;
    }

    public function build(): array
    {
        if (!$this->nickname || !$this->type) {
            throw new \InvalidArgumentException('Required fields are missing');
        }

        // Validate based on account type
        if ($this->type === 'bank_account') {
            if (!$this->accountHolderName || !$this->accountNumber || !$this->ifscCode) {
                throw new \InvalidArgumentException('Bank account details are missing');
            }
        }

        if ($this->type === 'vpa' && !$this->virtualAddress) {
            throw new \InvalidArgumentException('Virtual address is required for VPA account type');
        }

        return [
            'nickname' => $this->nickname,
            'type' => $this->type,
            'account_number' => $this->accountNumber,
            'ifsc_code' => $this->ifscCode,
            'account_holder_name' => $this->accountHolderName,
            'virtual_address' => $this->virtualAddress,
        ];
    }
}
