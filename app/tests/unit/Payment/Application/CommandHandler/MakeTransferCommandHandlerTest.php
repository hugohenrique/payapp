<?php

declare(strict_types=1);

namespace App\Payment\Application\CommandHandler;

use App\Payment\Application\Command\MakeTransferCommand;
use App\Payment\Application\Service\TransactionExecutor;
use App\Payment\Domain\Exception\MerchantCannotTransferException;
use App\Payment\Domain\Exception\TransactionNotAuthorizedException;
use App\Payment\Domain\Model\Customer;
use App\Payment\Domain\Model\Merchant;
use App\Payment\Domain\Repository\UserRepository;
use App\Payment\Infrastructure\PaymentAuthorizerGateway;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Messenger\MessageBusInterface;

final class MakeTransferCommandHandlerTest extends TestCase
{
    /** @var UserRepository|MockObject */
    private UserRepository $userRepository;
    /** @var PaymentAuthorizerGateway|MockObject */
    private PaymentAuthorizerGateway $authorizerGateway;
    /** @var TransactionExecutor|MockObject */
    private TransactionExecutor $transactionExecutor;
    /** @var MessageBusInterface|MockObject */
    private MessageBusInterface $eventBus;
    private MakeTransferCommandHandler $handler;

    #[Before]
    public function configuraDependencies(): void
    {
        $this->userRepository      = $this->createMock(UserRepository::class);
        $this->authorizerGateway   = $this->createMock(PaymentAuthorizerGateway::class);
        $this->transactionExecutor = $this->createMock(TransactionExecutor::class);
        $this->eventBus            = $this->createMock(MessageBusInterface::class);
        $this->handler = new MakeTransferCommandHandler(
            $this->userRepository,
            $this->authorizerGateway,
            $this->transactionExecutor,
            $this->eventBus
        );
    }

    public function testShouldRaiseExceptionWhenPayerIsMerchant(): void
    {
        $this->expectException(MerchantCannotTransferException::class);

        $transferId = Uuid::uuid4();
        $payerId    = Uuid::uuid4();
        $payeeId    = Uuid::uuid4();
        $amount     = 100.00;

        $merchant = new Merchant($payerId, 'João', '892.092.231-09', 'joao.silva@gmail.com', '1234');

        $this->userRepository->expects($this->once())
                             ->method('loadById')
                             ->with($payerId)
                             ->willReturn($merchant);

        $command = new MakeTransferCommand($transferId, $payerId, $payeeId, $amount);

        $this->handler->__invoke($command);
    }

    public function testShouldRaiseExceptionWhenPaymentNotAuthorized(): void
    {
        $this->expectException(TransactionNotAuthorizedException::class);

        $transferId = Uuid::uuid4();
        $payerId    = Uuid::uuid4();
        $payeeId    = Uuid::uuid4();
        $amount     = 100.00;

        $customer = new Customer($payerId, 'João', '892.092.231-09', 'joao.silva@gmail.com', '1234');

        $this->userRepository->expects($this->once())
                             ->method('loadById')
                             ->with($payerId)
                             ->willReturn($customer);

        $this->authorizerGateway->expects($this->once())
                                ->method('authorize')
                                ->with($transferId->toString())
                                ->willReturn(false);

        $command = new MakeTransferCommand($transferId, $payerId, $payeeId, $amount);

        $this->handler->__invoke($command);
    }
}
