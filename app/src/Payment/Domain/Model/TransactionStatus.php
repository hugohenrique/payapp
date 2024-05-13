<?php

declare(strict_types=1);

namespace App\Payment\Domain\Model;

enum TransactionStatus: string
{
    case PENDING   = 'pending';
    case COMPLETED = 'completed';
    case FAILED    = 'failed';
    case REVERTED  = 'reverted';
}