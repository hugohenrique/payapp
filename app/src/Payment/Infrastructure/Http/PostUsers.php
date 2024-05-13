<?php

declare(strict_types=1);

namespace App\Payment\Infrastructure\Http;

use App\Payment\Application\Command\CreateUserCommand;
use App\Payment\Application\Service\Validator\UserValidator;
use App\Payment\Domain\Model\UserType;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Messenger\MessageBusInterface;

use function count;

#[AsController]
final class PostUsers
{
    public function __construct(
        private readonly MessageBusInterface $commandBus,
        private readonly UserValidator $validator
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true);

        $violations = $this->validator->validate($payload);

        if (count($violations)) {
            throw new HttpException(400, (string) $violations);
        }

        $userId = Uuid::uuid4();

        $this->commandBus->dispatch(
            new CreateUserCommand(
                id: $userId,
                email: $payload['email'],
                password: $payload['password'],
                fullName: $payload['name'],
                taxpayerNumber: $payload['taxpayerNumber'],
                type: UserType::from($payload['type'])
            )
        );

        $response = new JsonResponse(null, 204);
        $response->headers->set('Location', '/api/users/' . (string) $userId);

        return $response;
    }
}