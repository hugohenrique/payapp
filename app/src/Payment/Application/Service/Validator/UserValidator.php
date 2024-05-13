<?php

declare(strict_types=1);

namespace App\Payment\Application\Service\Validator;

use App\Payment\Domain\Model\UserType;
use App\Payment\Domain\Repository\UserRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class UserValidator
{
    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly UserRepository $userRepository
    ) {
    }

    public function validate(array $payload): ConstraintViolationListInterface
    {
        $asserts = [
            'allowExtraFields' => true,
            'fields' => [
                'name' => new Assert\Required(),
                'taxpayerNumber' => [
                    new Assert\Required(),
                    new Assert\Callback(
                        function (mixed $value, ExecutionContextInterface $context, mixed $payload): void {
                            if ($this->userRepository->loadByTaxpayerNumber($value)) {
                                $context->buildViolation('The CPF already exists')
                                        ->addViolation();
                            }
                        }
                    )
                ],
                'email' => [
                    new Assert\Required(),
                    new Assert\Email(),
                    new Assert\Callback(
                        function (mixed $value, ExecutionContextInterface $context, mixed $payload): void {
                            if ($this->userRepository->loadByEmail($value)) {
                                $context->buildViolation('The email already exists')
                                        ->addViolation();
                            }
                        }
                    )
                ],
                'password' => [
                    new Assert\Required(),
                    new Assert\PasswordStrength()
                ],
                'type' => [
                    new Assert\Required(),
                    new Assert\Choice(['choices' => [UserType::CUSTOMER, UserType::MERCHANT]])
                ]
            ]
        ];

        return $this->validator->validate($payload, new Assert\Collection($asserts));
    }
}
