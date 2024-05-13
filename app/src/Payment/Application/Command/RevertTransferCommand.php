<?php

declare(strict_types=1);

namespace App\Payment\Application\Command;

use Ramsey\Uuid\UuidInterface;

final class RevertTransferCommand
{
    public function __construct(public readonly UuidInterface $sourceId)
    {
    }
}
