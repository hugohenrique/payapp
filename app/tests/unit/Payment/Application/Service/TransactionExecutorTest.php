<?php

declare(strict_types=1);

namespace App\Payment\Application\Service;

use App\Payment\Domain\Model\Customer;
use App\Payment\Domain\Model\FinancialTransaction;
use App\Payment\Domain\Model\TransactionStatus;
use App\Payment\Domain\Model\Wallet;
use App\Payment\Domain\Repository\FinancialTransactionRepository;
use App\Payment\Domain\Repository\WalletRepository;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Nonstandard\Uuid;

final class TransactionExecutorTest extends TestCase
{
    /** @var WalletRepository|MockObject */
    private WalletRepository $walletRepository;
    /** @var FinancialTransactionRepository|MockObject */
    private FinancialTransactionRepository $transactionRepository;
    private TransactionExecutor $executor;

    #[Before]
    public function configuraDependencies(): void
    {
        $this->walletRepository      = $this->createMock(WalletRepository::class);
        $this->transactionRepository = $this->createMock(FinancialTransactionRepository::class);

        $this->executor = new TransactionExecutor($this->transactionRepository, $this->walletRepository);
    }

    public function testExecuteShouldChangeTheWalletsAndCreateTransaction(): void
    {
        $payerId = Uuid::uuid4();
        $payeeId = Uuid::uuid4();
        $walletIdPayer = Uuid::uuid4();
        $walletIdPayee = Uuid::uuid4();
        $amount = 20.0;

        $encryptedPass = password_hash('pass123', PASSWORD_BCRYPT);

        $payer = new Customer($payerId, 'João', '234.340.003-21', 'joao.loureiro@gmail.com', $encryptedPass);
        $payee = new Customer($payeeId, 'Luis', '130.540.011-11', 'luiz.silva@gmail.com', $encryptedPass);

        $walletPayer = new Wallet($walletIdPayer, $payer, 9302, 120.00);
        $walletPayer->subtract($amount);

        $walletPayee = new Wallet($walletIdPayee, $payee, 2302, 100.00);
        $walletPayee->add($amount);

        $this->walletRepository->expects($this->atLeast(0))
                              ->method('loadByUserId')
                              ->with($payerId)
                              ->willReturn($walletPayer);

        $this->walletRepository->expects($this->atLeast(1))
                              ->method('loadByUserId')
                              ->with($payeeId)
                              ->willReturn($walletPayee);

        $transactionId = Uuid::uuid4();

        $this->walletRepository->expects($this->atLeast(0))
                               ->method('save')
                               ->with($walletPayer);

        $this->walletRepository->expects($this->atLeast(1))
                               ->method('save')
                               ->with($walletPayee);

        $transaction = new FinancialTransaction(
            $transactionId,
            $payer,
            $payee,
            $amount,
            TransactionStatus::COMPLETED
        );

        $this->transactionRepository->expects($this->once())
                                    ->method('save')
                                    ->with($transaction);

        $transaction = $this->executor->execute(
            $transactionId,
            $payerId,
            $payeeId,
            $amount
        );

        $this->assertEquals($transaction->getAmount(), $amount);
    }
}