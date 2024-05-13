<?php

declare(strict_types=1);

namespace App\Payment\Infrastructure;

class ExternalMailer
{
    public function send(string $receipt, string $subject, array $data): void
    {
        echo 'Hi, ' . $receipt;
        echo "\n";
        echo "---------------------------------------------------------------\n";
        echo $subject;
        echo "\n---------------------------------------------------------------";
        echo "\n";
    }
}
