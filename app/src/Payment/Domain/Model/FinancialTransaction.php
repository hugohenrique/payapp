<?php

declare(strict_types=1);

namespace App\Payment\Domain\Model;

use DateTimeImmutable;
use DateTimeInterface;
use Ramsey\Uuid\UuidInterface;

class FinancialTransaction
{
    private DateTimeInterface $createdAt;
    private DateTimeInterface|null $updatedAt = null;
    private FinancialTransaction|null $source = null;

    public function __construct(
        private UuidInterface $id,
        private Customer $payer,
        private Customer $payee,
        private float $amount,
        private TransactionStatus $status
    ) {
        $this->createdAt = new DateTimeImmutable();
    }

    public static function buildRevert(
        FinancialTransaction $source,
        UuidInterface $id,
        float $amount,
        Customer $payer,
        Customer $payee,
    ): self {
        $transaction = new self($id, $payer, $payee, $amount, TransactionStatus::REVERTED);
        $transaction->source = $source;

        return $transaction;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getPayer(): User
    {
        return $this->payer;
    }

    public function getPayee(): User
    {
        return $this->payee;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getStatus(): TransactionStatus
    {
        return $this->status;
    }
}
