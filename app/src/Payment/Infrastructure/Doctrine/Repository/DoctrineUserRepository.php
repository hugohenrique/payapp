<?php

declare(strict_types=1);

namespace App\Payment\Infrastructure\Doctrine\Repository;

use App\Payment\Domain\Model\User;
use App\Payment\Domain\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Ramsey\Uuid\UuidInterface;

class DoctrineUserRepository implements UserRepository
{
    private EntityRepository $repository;

    public function __construct(private EntityManagerInterface $em)
    {
        $this->repository = $em->getRepository(User::class);
    }

    public function save(User $user): void
    {
        $this->em->persist($user);
        $this->em->flush();
    }

    public function loadById(UuidInterface $id): User|null
    {
        return $this->repository->find($id->toString());
    }

    public function loadByEmail(string $email): User|null
    {
        return $this->repository->findOneBy(['email' => $email]);
    }

    public function loadByTaxpayerNumber(string $taxpayerNumber): User|null
    {
        return $this->repository->findOneBy(['taxpayerNumber' => $taxpayerNumber]);
    }
}
