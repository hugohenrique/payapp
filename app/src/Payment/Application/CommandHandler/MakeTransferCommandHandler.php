<?php

declare(strict_types=1);

namespace App\Payment\Application\CommandHandler;

use App\Payment\Application\Command\MakeTransferCommand;
use App\Payment\Application\CommandHandler;
use App\Payment\Application\Event\TransferWasCompleted;
use App\Payment\Application\Service\TransactionExecutor;
use App\Payment\Domain\Exception\MerchantCannotTransferException;
use App\Payment\Domain\Exception\TransactionNotAuthorizedException;
use App\Payment\Domain\Model\Merchant;
use App\Payment\Domain\Repository\UserRepository;
use App\Payment\Infrastructure\PaymentAuthorizerGateway;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;

final class MakeTransferCommandHandler implements CommandHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private PaymentAuthorizerGateway $authorizerGateway,
        private TransactionExecutor $transactionExecutor,
        private MessageBusInterface $eventBus
    ) {
    }

    public function __invoke(MakeTransferCommand $command): void
    {
        $payer = $this->userRepository->loadById($command->payerId);

        if ($payer instanceof Merchant) {
            throw new MerchantCannotTransferException('Merchant user cannot make transfer, only receive');
        }

        if (!$this->authorizerGateway->authorize($command->id->toString())) {
            throw new TransactionNotAuthorizedException();
        }

        $transaction = $this->transactionExecutor->execute(
            $command->id,
            $command->payerId,
            $command->payeeId,
            $command->amount
        );

        $envelop = new Envelope(new TransferWasCompleted($transaction->getId()));
        $envelop->with(new DispatchAfterCurrentBusStamp());

        $this->eventBus->dispatch($envelop);
    }
}
