<?php

declare(strict_types=1);

namespace App\Payment\Infrastructure\Doctrine\Repository;

use App\Payment\Domain\Model\Wallet;
use App\Payment\Domain\Repository\WalletRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Ramsey\Uuid\UuidInterface;

class DoctrineWalletRepository implements WalletRepository
{
    private EntityRepository $repository;

    public function __construct(private EntityManagerInterface $em)
    {
        $this->repository = $em->getRepository(Wallet::class);
    }

    public function loadByUserId(UuidInterface $userId): Wallet|null
    {
        return $this->repository->findOneBy(['user' => $userId]);
    }

    public function save(Wallet $wallet): void
    {
        $this->em->persist($wallet);
        $this->em->flush();
    }
}
