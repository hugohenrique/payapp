<?php

declare(strict_types=1);

namespace App\Payment\Domain\Exception;

use DomainException;

final class InsufficientFundsException extends DomainException
{
}
