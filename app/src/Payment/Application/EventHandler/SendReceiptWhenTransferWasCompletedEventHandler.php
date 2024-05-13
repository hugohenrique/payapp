<?php

declare(strict_types=1);

namespace App\Payment\Application\EventHandler;

use App\Payment\Application\Event\TransferWasCompleted;
use App\Payment\Application\EventHandler;
use App\Payment\Domain\Repository\FinancialTransactionRepository;
use App\Payment\Infrastructure\ExternalMailer;

use function number_format;

class SendReceiptWhenTransferWasCompletedEventHandler implements EventHandler
{
    public function __construct(
        private FinancialTransactionRepository $transactionRepository,
        private ExternalMailer $mailer
    ) {
    }

    public function __invoke(TransferWasCompleted $event): void
    {
        $transaction = $this->transactionRepository->loadById($event->transactionId);

        $this->mailer->send(
            $transaction->getPayer()->getEmail(),
            'Congrats! Your payment of R$ ' . number_format($transaction->getAmount(), 2) . ' was sent! :)',
            [
                'transactionId' => $transaction->getId(),
                'amount'        => $transaction->getAmount()
            ]
        );
    }
}