<?php

namespace ItsTreason\AptRepo\Api\Ui\PackageList;

use ItsTreason\AptRepo\FileStorage\FileStorageInterface;
use ItsTreason\AptRepo\Repository\PackageMetadataRepository;
use ItsTreason\AptRepo\Repository\SuitePackagesRepository;
use ItsTreason\AptRepo\Service\PackageListService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteContext;

class PackageDeleteController
{
    public function __construct(
        private readonly SuitePackagesRepository   $suitePackagesRepository,
        private readonly PackageMetadataRepository $packageMetadataRepository,
        private readonly PackageListService        $listService,
        private readonly FileStorageInterface      $fileStorage,
    ) {}

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();

        $packageName = $route?->getArgument('packageName');
        $package = $this->packageMetadataRepository->getPackageByFilename($packageName);

        $suites = $this->suitePackagesRepository->getAllSuitesForPackage($package);
        $this->suitePackagesRepository->removePackageFromAllSuites($package);

        $this->fileStorage->deleteFile($package->getId());
        $this->packageMetadataRepository->deletePackage($package);

        foreach ($suites as $suite) {
            $this->listService->updatePackageLists($suite);
        }

        return $response->withHeader('Location', '/ui/packages')
            ->withStatus(302);
    }
}
