<?php

declare(strict_types=1);

namespace App\Payment\Application\EventHandler;

use App\Payment\Application\Event\TransferWasReverted;
use App\Payment\Application\EventHandler;
use App\Payment\Domain\Repository\FinancialTransactionRepository;
use App\Payment\Infrastructure\ExternalMailer;

use function number_format;

final class SendReceiptWhenTransferWasRevertedEventHandler implements EventHandler
{
    public function __construct(
        private FinancialTransactionRepository $transactionRepository,
        private ExternalMailer $mailer
    ) {
    }

    public function __invoke(TransferWasReverted $event): void
    {
        $transaction = $this->transactionRepository->loadById($event->transactionId());

        $this->mailer->send(
            $transaction->getPayer()->getEmail(),
            '/!\ Your payment of R$ ' . number_format($transaction->getAmount(), 2) . ' was reverted /!\'',
            [
                'transactionId' => $transaction->getId(),
                'amount'        => $transaction->getAmount()
            ]
        );
    }
}