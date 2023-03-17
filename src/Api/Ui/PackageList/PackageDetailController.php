<?php

namespace ItsTreason\AptRepo\Api\Ui\PackageList;

use ItsTreason\AptRepo\Repository\PackageMetadataRepository;
use ItsTreason\AptRepo\Repository\SuitePackagesRepository;
use ItsTreason\AptRepo\Repository\SuitesRepository;
use ItsTreason\AptRepo\Value\Suite;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteContext;
use Twig\Environment;

class PackageDetailController
{
    public function __construct(
        private readonly Environment               $twig,
        private readonly SuitePackagesRepository   $suitePackagesRepository,
        private readonly SuitesRepository          $suitesRepository,
        private readonly PackageMetadataRepository $packageMetadataRepository,
    ) {}

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();

        $packageName = $route?->getArgument('packageName');

        $packageMetadata = $this->packageMetadataRepository->getPackageByFilename($packageName);
        if ($packageMetadata === null) {
            return $response->withStatus(404);
        }

        $loggedIn = isset($request->getCookieParams()['apiKey']);

        $currentSuites = $this->suitePackagesRepository->getAllPackagesForPackage($packageMetadata);

        $allSuites = $this->suitesRepository->getAll();

        /** @var Suite[] $availableSuites */
        $availableSuites = [];
        foreach ($allSuites as $suite) {
            if (!in_array($suite, $currentSuites)) {
                $availableSuites[] = $suite;
            }
        }

        $body = $this->twig->render('PackageDetail/packageDetail.twig', [
            'package' => $packageMetadata,
            'currentSuites' => $currentSuites,
            'availableSuites' => $availableSuites,
            'loggedIn' => $loggedIn,
        ]);

        $response->getBody()->write($body);

        return $response;
    }
}
