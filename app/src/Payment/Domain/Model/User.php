<?php

declare(strict_types=1);

namespace App\Payment\Domain\Model;

use Ramsey\Uuid\UuidInterface;

abstract class User
{
    public function __construct(
        protected UuidInterface $id,
        protected string $fullName,
        protected string $taxpayerNumber,
        protected string $email,
        protected string $password
    ) {
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
