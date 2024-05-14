<?php

declare(strict_types=1);

namespace App\Payment\Infrastructure;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class PaymentAuthorizerGateway
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private string $baseUrl
    ) {
    }

    public function authorize(string $transactionId): bool
    {
        /*
        $uri = '/external-payment-authorizer' . '/' . $transactionId;

        $response = $this->httpClient->request(
            'GET',
            $this->baseUrl . $uri
        );

        $content = $response->getContent();

        return isset($content['message']) && $content['message'] === 'Autorizado';
        */

        return true;
    }
}