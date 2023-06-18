<?php

namespace ItsTreason\AptRepo\Api\Ui\PackageList;

use ItsTreason\AptRepo\Repository\PackageMetadataRepository;
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
        $search = $request->getQueryParams()['search'] ?? '';
        $groupPackages = isset($request->getQueryParams()['groupPackages']);

        if ($groupPackages) {
            $packages = $this->packageMetadataRepository->getAllPackageNames($search);
        } else {
            $packagesMetadata = $this->packageMetadataRepository->getAllPackages($search);
            $packages = array_map(static fn ($metadata) => $metadata->getFilename(), $packagesMetadata);
        }

        $body = $this->twig->render('packageList.twig', [
            'packages' => $packages,
            'groupPackages' => $groupPackages,
            'search' => $search,
        ]);

        $response->getBody()->write($body);

        return $response;
    }
}
