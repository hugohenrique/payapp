<?php

declare(strict_types=1);

namespace App\Payment\Application\Service;

use App\Payment\Domain\Model\FinancialTransaction;
use App\Payment\Domain\Model\TransactionStatus;
use App\Payment\Domain\Repository\FinancialTransactionRepository;
use App\Payment\Domain\Repository\WalletRepository;
use Ramsey\Uuid\UuidInterface;

class TransactionExecutor
{
    public function __construct(
        private FinancialTransactionRepository $transactionRepository,
        private WalletRepository $walletRepository
    ) {
    }

    public function execute(
        UuidInterface $transactionId,
        UuidInterface $payerId,
        UuidInterface $payeeId,
        float $amount,
    ): FinancialTransaction {
        $payerWallet = $this->walletRepository->loadByUserId($payerId);
        $payerWallet->subtract($amount);

        $payeeWallet = $this->walletRepository->loadByUserId($payeeId);
        $payeeWallet->add($amount);

        $this->walletRepository->save($payerWallet);
        $this->walletRepository->save($payeeWallet);

        $transaction = new FinancialTransaction(
            $transactionId,
            $payerWallet->getUser(),
            $payeeWallet->getUser(),
            $amount,
            TransactionStatus::COMPLETED
        );

        $this->transactionRepository->save($transaction);

        return $transaction;
    }
}
