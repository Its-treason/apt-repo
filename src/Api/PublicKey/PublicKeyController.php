<?php

namespace ItsTreason\AptRepo\Api\PublicKey;

use ItsTreason\AptRepo\Service\GpgSignService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class PublicKeyController
{
    public function __construct(
        private readonly GpgSignService $gpgSignService,
    ) {}

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $publicKey = $this->gpgSignService->getPublicKey();

        $response->getBody()->write($publicKey);
        return $response->withHeader('Content-Type', 'text/plain')
          ->withHeader('Cache-Control', 'public, max-age=600')
          ->withStatus(200);
    }
}
