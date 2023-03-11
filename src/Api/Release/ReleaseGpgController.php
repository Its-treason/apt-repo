<?php

namespace ItsTreason\AptRepo\Api\Release;

use ItsTreason\AptRepo\Service\GpgSignService;
use ItsTreason\AptRepo\Service\ReleaseFileService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteContext;

class ReleaseGpgController
{
    public function __construct(
        private readonly ReleaseFileService $releaseFileService,
        private readonly GpgSignService $gpgSignService,
    ) {}

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();

        $codename = $route?->getArgument('codename');

        $release = $this->releaseFileService->createReleaseFile($codename);

        $gpg = $this->gpgSignService->createReleaseGpg($release);

        $response->getBody()->write($gpg);
        return $response->withHeader('Content-Type', 'text/plain')->withStatus(200);
    }
}
