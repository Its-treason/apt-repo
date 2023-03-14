<?php

namespace ItsTreason\AptRepo\Api\Ui\PackageList;

use ItsTreason\AptRepo\Repository\PackageMetadataRepository;
use ItsTreason\AptRepo\Repository\SuitePackagesRepository;
use ItsTreason\AptRepo\Repository\SuitesRepository;
use ItsTreason\AptRepo\Service\PackageListService;
use ItsTreason\AptRepo\Value\Suite;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteContext;
use Twig\Environment;

class PackageSuiteAddController
{
    public function __construct(
        private readonly SuitePackagesRepository   $suitePackagesRepository,
        private readonly PackageMetadataRepository $packageMetadataRepository,
        private readonly PackageListService        $listService,
    ) {}

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $body = $request->getParsedBody();
        if (empty($body['suite'])) {
            return $response->withStatus(302)->withHeader('Location', '/ui/suites?error=Missing values');
        }

        [$codename, $suite] = explode('-', $body['suite']);
        $suite = Suite::fromValues($codename, $suite);
        // TODO: Check if suite exists

        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();

        $packageName = $route?->getArgument('packageName');

        $package = $this->packageMetadataRepository->getPackageByFilename($packageName);

        $this->suitePackagesRepository->insertPackageIntoSuite($package, $suite);

        $this->listService->updatePackageLists($suite);

        return $response->withHeader('Location', '/ui/packages/' . $package->getFilename())
            ->withStatus(302);
    }
}
