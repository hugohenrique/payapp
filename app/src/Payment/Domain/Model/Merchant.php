<?php

declare(strict_types=1);

namespace App\Payment\Domain\Model;

use Ramsey\Uuid\UuidInterface;

class Merchant extends User
{
    public function __construct(
        UuidInterface $id,
        string $fullName,
        string $taxpayerNumber,
        string $email,
        string $password
    ) {
        parent::__construct(
            $id,
            $fullName,
            $taxpayerNumber,
            $email,
            $password,
            UserType::MERCHANT
        );
    }
}
