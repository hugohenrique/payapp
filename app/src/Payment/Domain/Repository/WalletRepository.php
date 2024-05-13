<?php

declare(strict_types=1);

namespace App\Payment\Domain\Repository;

use App\Payment\Domain\Model\Wallet;
use Ramsey\Uuid\UuidInterface;

interface WalletRepository
{
    public function loadByUserId(UuidInterface $userId): Wallet|null;
    public function save(Wallet $wallet): void;
}