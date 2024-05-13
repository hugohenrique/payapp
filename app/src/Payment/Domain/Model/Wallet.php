<?php

declare(strict_types=1);

namespace App\Payment\Domain\Model;

use App\Payment\Domain\Exception\InsufficientFundsException;
use DomainException;
use Ramsey\Uuid\UuidInterface;

class Wallet
{
    public function __construct(
        private UuidInterface $id,
        private User $user,
        private int $accountNumber,
        private float $currentBalance
    ) {
    }

    public function subtract(float $amount): void
    {
        if ($this->currentBalance <= 0 || $this->currentBalance < $amount) {
            throw new InsufficientFundsException();
        }

        $this->currentBalance -= $amount;
    }

    public function add(float $amount): void
    {
        if ($amount <= 0) {
            throw new DomainException('The amount should be a positive value');
        }

        $this->currentBalance += $amount;
    }

    public function getBalance(): float
    {
        return $this->currentBalance;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
