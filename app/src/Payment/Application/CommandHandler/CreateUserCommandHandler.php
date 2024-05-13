<?php

declare(strict_types=1);

namespace App\Payment\Application\CommandHandler;

use App\Payment\Application\Command\CreateUserCommand;
use App\Payment\Application\CommandHandler;
use App\Payment\Domain\Model\Customer;
use App\Payment\Domain\Model\Merchant;
use App\Payment\Domain\Model\UserType;
use App\Payment\Domain\Model\Wallet;
use App\Payment\Domain\Repository\UserRepository;
use App\Payment\Domain\Repository\WalletRepository;
use Ramsey\Uuid\Uuid;

use function rand;
use function password_hash;

use const PASSWORD_BCRYPT;

final class CreateUserCommandHandler implements CommandHandler
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly WalletRepository $walletRepository
    ) {
    }

    public function __invoke(CreateUserCommand $command): void
    {
        $encryptedPass = password_hash($command->password, PASSWORD_BCRYPT);

        if ($command->type === UserType::CUSTOMER) {
            $user = new Customer(
                $command->id,
                $command->fullName,
                $command->taxpayerNumber,
                $command->email,
                $encryptedPass
            );
        } elseif ($command->type === UserType::MERCHANT) {
            $user = new Merchant(
                $command->id,
                $command->fullName,
                $command->taxpayerNumber,
                $command->email,
                $encryptedPass
            );
        }

        $this->userRepository->save($user);

        $wallet = new Wallet(Uuid::uuid4(), $user, rand(8, 10), 0.0);

        $this->walletRepository->save($wallet);
    }
}
