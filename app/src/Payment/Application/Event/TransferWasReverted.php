<?php

declare(strict_types=1);

namespace App\Payment\Application\Event;

use Ramsey\Uuid\UuidInterface;

final class TransferWasReverted
{
    public function __construct(private UuidInterface $transactionId)
    {
    }

    public function transactionId(): UuidInterface
    {
        return $this->transactionId;
    }
}
