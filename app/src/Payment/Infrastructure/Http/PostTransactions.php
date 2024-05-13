<?php

declare(strict_types=1);

namespace App\Payment\Infrastructure\Http;

use App\Payment\Application\Command\MakeTransferCommand;
use App\Payment\Application\Service\Validator\TransactionValidator;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Throwable;

use function count;

#[AsController]
final class PostTransactions
{
    public function __construct(
        private readonly MessageBusInterface $commandBus,
        private readonly TransactionValidator $validator
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true);

        $violations = $this->validator->validate($payload);

        if (count($violations)) {
            throw new HttpException(400, (string) $violations);
        }

        $transactionId = Uuid::uuid4();

        try {
            $this->commandBus->dispatch(
                new MakeTransferCommand(
                    id: $transactionId,
                    payerId: Uuid::fromString($payload['payer']),
                    payeeId: Uuid::fromString($payload['payee']),
                    amount: (float) $payload['amount']
                )
            );

            $response = new JsonResponse(null, 204);
            $response->headers->set('Location', '/api/transactions/' . (string) $transactionId);
        } catch (Throwable $e) {
            $response = new JsonResponse('Error to create the transaction', 400);
            dd($e);
        }

        return $response;
    }
}