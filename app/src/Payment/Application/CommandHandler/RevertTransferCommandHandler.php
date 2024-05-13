<?php

declare(strict_types=1);

namespace App\Payment\Application\CommandHandler;

use App\Payment\Application\Command\RevertTransferCommand;
use App\Payment\Application\CommandHandler;
use App\Payment\Application\Service\TransactionReversor;
use App\Payment\Domain\Exception\TransactionAlreadyRevertedException;
use App\Payment\Domain\Exception\TransactionNotAuthorizedException;
use App\Payment\Domain\Model\TransactionStatus;
use App\Payment\Domain\Repository\FinancialTransactionRepository;
use App\Payment\Domain\Repository\UserRepository;
use App\Payment\Infrastructure\PaymentAuthorizerGateway;
use Ramsey\Uuid\Uuid;

final class RevertTransferCommandHandler implements CommandHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private FinancialTransactionRepository $transactionRepository,
        private PaymentAuthorizerGateway $authorizerGateway,
        private TransactionReversor $transactionReversor
    ) {
    }

    public function __invoke(RevertTransferCommand $command): void
    {
        $source = $this->transactionRepository->loadById($command->sourceId);

        if ($source->getStatus() === TransactionStatus::REVERTED) {
            throw new TransactionAlreadyRevertedException();
        }

        if (!$this->authorizerGateway->authorize((string) $command->sourceId)) {
            throw new TransactionNotAuthorizedException();
        }

        $transactionId = Uuid::uuid4();

        $this->transactionReversor->reverse(
            $transactionId,
            $source->getId(),
            $source->getPayer()->getId(),
            $source->getPayee()->getId()
        );
    }
}
