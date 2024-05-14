<?php

declare(strict_types=1);

namespace App\Payment\Application\Service;

use App\Payment\Domain\Model\Customer;
use App\Payment\Domain\Model\FinancialTransaction;
use App\Payment\Domain\Model\Wallet;
use App\Payment\Domain\Repository\FinancialTransactionRepository;
use App\Payment\Domain\Repository\WalletRepository;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Nonstandard\Uuid;
use Ramsey\Uuid\UuidInterface;

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
        $walletIdPayer = Uuid::uuid4();
        $walletIdPayee = Uuid::uuid4();
        $amount = 20.0;

        $encryptedPass = password_hash('pass123', PASSWORD_BCRYPT);

        $payerId = Uuid::uuid4();
        $payer = new Customer($payerId, 'João', '234.340.003-21', 'joao.loureiro@gmail.com', $encryptedPass);

        $payeeId = Uuid::uuid4();
        $payee = new Customer($payeeId, 'Luis', '130.540.011-11', 'luiz.silva@gmail.com', $encryptedPass);

        $walletPayer = new Wallet($walletIdPayer, $payer, 9302, 120.00);
        $walletPayer->subtract($amount);

        $walletPayee = new Wallet($walletIdPayee, $payee, 2302, 100.00);
        $walletPayee->add($amount);

        $this->walletRepository->expects($this->atLeast(0))
                              ->method('loadByUserId')
                              ->with($this->isInstanceOf(UuidInterface::class))
                              ->willReturn($walletPayer);

        $this->walletRepository->expects($this->atLeast(1))
                              ->method('loadByUserId')
                              ->with($this->isInstanceOf(UuidInterface::class))
                              ->willReturn($walletPayee);

        $this->walletRepository->expects($this->atLeast(0))
                               ->method('save')
                               ->with($this->isInstanceOf(Wallet::class));

        $this->walletRepository->expects($this->atLeast(1))
                               ->method('save')
                               ->with($this->isInstanceOf(Wallet::class));

        $this->transactionRepository->expects($this->once())
                                    ->method('save')
                                    ->with($this->isInstanceOf(FinancialTransaction::class));

        $transactionId = Uuid::uuid4();

        $this->executor->execute(
            $transactionId,
            $payerId,
            $payeeId,
            $amount
        );
    }
}