<?php

declare(strict_types=1);

namespace App\Payment\Application\Command;

use App\Payment\Domain\Model\UserType;
use Ramsey\Uuid\UuidInterface;

final class CreateUserCommand
{
    public function __construct(
        public readonly UuidInterface $id,
        public readonly string $fullName,
        public readonly string $taxpayerNumber,
        public readonly string $email,
        public readonly string $password,
        public readonly UserType $type
    ) {
    }
}
