<?php

declare(strict_types=1);

namespace App\Payment\Application\Event;

use Ramsey\Uuid\UuidInterface;

final class TransferWasCompleted
{
    public function __construct(public readonly UuidInterface $transactionId)
    {
    }
}
