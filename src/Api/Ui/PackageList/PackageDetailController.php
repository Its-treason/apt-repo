<?php

namespace ItsTreason\AptRepo\Api\Ui\PackageList;

use ItsTreason\AptRepo\Repository\PackageMetadataRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteContext;
use Twig\Environment;

class PackageDetailController
{
    public function __construct(
        private readonly Environment               $twig,
        private readonly PackageMetadataRepository $packageMetadataRepository,
    ) {}

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();

        $packageName = $route?->getArgument('packageName');

        $packagesMetadata = $this->packageMetadataRepository->getAllPackageVersionsByPackageName($packageName);

        if (count($packagesMetadata) === 0) {
            return $response->withStatus(404);
        }

        $packages = [];
        foreach ($packagesMetadata as $packageMetadata) {
            $packages[] = [
                'arch' => $packageMetadata->getArch(),
                'filename' => $packageMetadata->getFilename(),
                'version' => $packageMetadata->getVersion(),
            ];
        }

        $body = $this->twig->render('packageDetail.twig', [
            'packages' => $packages,
            'packageName' => $packageName,
        ]);

        $response->getBody()->write($body);

        return $response;
    }
}
