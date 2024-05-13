<?php

declare(strict_types=1);

namespace App\Payment\Application\Command;

use DomainException;
use Ramsey\Uuid\UuidInterface;

final class MakeTransferCommand
{
    public function __construct(
        public readonly UuidInterface $id,
        public readonly UuidInterface $payerId,
        public readonly UuidInterface $payeeId,
        public readonly float $amount
    ) {
        if ($amount <= 0) {
            throw new DomainException('You should informs the positive amount');
        }
    }
}
