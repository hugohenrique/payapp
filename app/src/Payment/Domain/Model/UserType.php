<?php

declare(strict_types=1);

namespace App\Payment\Domain\Model;

enum UserType: string
{
    case CUSTOMER = 'customer';
    case MERCHANT = 'merchant';
}