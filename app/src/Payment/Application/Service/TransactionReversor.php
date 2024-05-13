<?php

declare(strict_types=1);

namespace App\Payment\Application\Service;

use App\Payment\Application\Event\TransferWasReverted;
use App\Payment\Domain\Model\Transaction;
use App\Payment\Domain\Repository\FinancialTransactionRepository;
use App\Payment\Domain\Repository\WalletRepository;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;

class TransactionReversor
{
    public function __construct(
        private FinancialTransactionRepository $transactionRepository,
        private WalletRepository $walletRepository,
        private MessageBusInterface $eventBus
    ) {
    }

    public function reverse(
        UuidInterface $transactionId,
        UuidInterface $sourceId,
        UuidInterface $payerId,
        UuidInterface $payeeId
    ): void {
        $source = $this->transactionRepository->loadById($sourceId);
        $amount = $source->getAmount();

        $payerWallet = $this->walletRepository->loadByUserId($payerId);
        $payerWallet->add($amount);
        $this->walletRepository->save($payerWallet);

        $payeeWallet = $this->walletRepository->loadByUserId($payeeId);
        $payeeWallet->subtract($amount);
        $this->walletRepository->save($payeeWallet);

        $transaction = Transaction::buildRevert(
            $source,
            $transactionId,
            $amount,
            $payerWallet->getUser(),
            $payeeWallet->getUser()
        );

        $this->transactionRepository->save($transaction);

        $envelop = new Envelope(new TransferWasReverted($transactionId));
        $envelop->with(new DispatchAfterCurrentBusStamp());

        $this->eventBus->dispatch($envelop);
    }
}
