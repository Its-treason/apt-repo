<?php

namespace ItsTreason\AptRepo\Api\Release;

use ItsTreason\AptRepo\Service\ReleaseFileService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ReleaseController
{
    public function __construct(
        private readonly ReleaseFileService $releaseFileService,
    ) {}

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $release = $this->releaseFileService->createReleaseFile();

        $response->getBody()->write($release);
        return $response->withHeader('Content-Type', 'text/plain')->withStatus(200);
    }
}
