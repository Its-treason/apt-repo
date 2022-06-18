<?php

namespace ItsTreason\AptRepo\Api\PackageList;

use ItsTreason\AptRepo\Api\Common\Repository\PackageMetadataRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig\Environment;

class PackageListController
{
    public function __construct(
        private readonly Environment $twig,
        private readonly PackageMetadataRepository $packageMetadataRepository,
    ) {}

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $packagesMetadata = $this->packageMetadataRepository->getAllPackages();

        $packages = [];
        foreach ($packagesMetadata as $packageMetadata) {
            $packages[] = $packageMetadata->getName();
        }

        $body = $this->twig->render('packageList.twig', [
            'packages' => $packages,
        ]);

        $response->getBody()->write($body);

        return $response;
    }
}
