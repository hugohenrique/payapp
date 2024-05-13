<?php

declare(strict_types=1);

namespace App\Payment\Infrastructure\Http;

use App\Payment\Application\Command\RevertTransferCommand;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Messenger\MessageBusInterface;
use Throwable;

#[AsController]
final class DeleteTransactions
{
    public function __construct(private readonly MessageBusInterface $commandBus)
    {
    }

    public function __invoke(string $id): JsonResponse
    {
        $transactionId = Uuid::fromString($id);

        try {
            $this->commandBus->dispatch(new RevertTransferCommand($transactionId));

            $response = new JsonResponse(null, 204);
            $response->headers->set('Location', '/api/transactions/' . (string) $transactionId);
        } catch (Throwable $e) {
            dd($e);
            $response = new JsonResponse('Error to create the transaction', 400);
        }

        return $response;
    }
}