<?php

declare(strict_types=1);

namespace App\Payment\Domain\Repository;

use App\Payment\Domain\Model\User;
use Ramsey\Uuid\UuidInterface;

interface UserRepository
{
    public function save(User $user): void;
    public function loadById(UuidInterface $id): User|null;
    public function loadByEmail(string $email): User|null;
    public function loadByTaxpayerNumber(string $taxpayerNumber): User|null;
}