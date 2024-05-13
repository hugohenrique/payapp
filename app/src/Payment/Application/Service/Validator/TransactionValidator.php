<?php

declare(strict_types=1);

namespace App\Payment\Application\Service\Validator;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class TransactionValidator
{
    public function __construct(private ValidatorInterface $validator)
    {
    }

    public function validate(array $payload): ConstraintViolationListInterface
    {
        $asserts = [
            'allowExtraFields' => true,
            'fields' => [
                'amount' => [
                    new Assert\Required(),
                    new Assert\Positive()
                ],
                'payer'  => new Assert\Required(),
                'payee'  => new Assert\Required(),
            ]
        ];

        return $this->validator->validate($payload, new Assert\Collection($asserts));
    }
}
