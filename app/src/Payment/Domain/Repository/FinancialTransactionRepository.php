<?php

declare(strict_types=1);

namespace App\Payment\Domain\Repository;

use App\Payment\Domain\Model\FinancialTransaction;
use Ramsey\Uuid\UuidInterface;

interface FinancialTransactionRepository
{
    public function loadById(UuidInterface $id): FinancialTransaction|null;
    public function save(FinancialTransaction $transaction): void;
}