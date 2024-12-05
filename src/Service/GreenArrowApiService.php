<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class GreenArrowApiService
{
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function registerDomain(string $domainName, string $type): bool
    {
        $response = $this->client->request('POST', 'https://greenarrow-api.example.com/v1/domains', [
            'json' => ['name' => $domainName, 'type' => $type],
        ]);
        return $response->getStatusCode() === 200;
    }
}
