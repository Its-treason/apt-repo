<?php

namespace ItsTreason\AptRepo\Api\Packages;

use ItsTreason\AptRepo\Repository\PackageListsRepository;
use ItsTreason\AptRepo\Value\Suite;
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

        $codename = $route?->getArgument('codename');
        $suite = $route?->getArgument('suite');
        $arch = $route?->getArgument('arch');
        // This is either 'Packages' or 'Packages.gz'
        $type = $route?->getArgument('package');

        $suite = Suite::fromValues($codename, $suite);
        // Remove the binary- Prefix
        $arch = str_replace('binary-', '', $arch);

        $packageList = $this->packageListsRepository->getPackageList($arch, $type, $suite);
        if ($packageList === null) {
            return $response->withStatus(404);
        }

        $response->getBody()->write($packageList->getContent());
        return $response->withHeader('Content-Type', 'application/octet-stream')
            ->withHeader('Cache-Control', 'public, max-age=600')
            ->withStatus(200);
    }
}
