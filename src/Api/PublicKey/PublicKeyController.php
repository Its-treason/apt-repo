<?php

namespace ItsTreason\AptRepo\Api\PublicKey;

use ItsTreason\AptRepo\Api\Common\Service\GpgSignService;
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
        return $response->withHeader('Content-Type', 'application/octet-stream')->withStatus(200);
    }
}
