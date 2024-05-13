<?php

declare(strict_types=1);

namespace App\Payment\Infrastructure\Doctrine\Repository;

use App\Payment\Domain\Model\FinancialTransaction;
use App\Payment\Domain\Repository\FinancialTransactionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Ramsey\Uuid\UuidInterface;

class DoctrineFinancialTransactionRepository implements FinancialTransactionRepository
{
    private EntityRepository $repository;

    public function __construct(private EntityManagerInterface $em)
    {
        $this->repository = $em->getRepository(FinancialTransaction::class);
    }

    public function loadById(UuidInterface $id): FinancialTransaction|null
    {
        return $this->repository->find($id);
    }

    public function save(FinancialTransaction $transaction): void
    {
        $this->em->persist($transaction);
        $this->em->flush();
    }
}
