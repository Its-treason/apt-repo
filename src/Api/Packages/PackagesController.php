<?php

namespace ItsTreason\AptRepo\Api\Packages;

use ItsTreason\AptRepo\Repository\PackageListsRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteContext;

class PackagesController
{
    public function __construct(
        private readonly PackageListsRepository $packageListsRepository,
    ) {}

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();

        $arch = $route?->getArgument('arch');
        // This is either 'Packages' or 'Packages.gz'
        $package = $route?->getArgument('package');

        $path = sprintf('main/%s/%s', $arch, $package);

        $packageList = $this->packageListsRepository->getPackageList($path);
        if ($packageList === null) {
            return $response->withStatus(404);
        }

        $response->getBody()->write($packageList->getContent());
        return $response->withHeader('Content-Type', 'application/octet-stream')
            ->withStatus(200);
    }
}
