<?php

namespace ItsTreason\AptRepo\Api\PackageDownload;

use ItsTreason\AptRepo\Api\Common\Repository\PackageMetadataRepository;
use ItsTreason\AptRepo\Api\Common\Service\StorjFileService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteContext;

class PackageDownloadController
{
    public function __construct(
        private StorjFileService $storjFileService,
        private PackageMetadataRepository $packageMetadataRepository,
    ) {}

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();

        $filename = $route?->getArgument('filename');
        if ($filename === null) {
            return $response->withStatus(404);
        }

        $packageMetadata = $this->packageMetadataRepository->getPackageByFilename($filename);
        if ($packageMetadata === null) {
            return $response->withStatus(404);
        }

        $downloadStream = $this->storjFileService->downloadFile($packageMetadata->getId());

        return $response->withBody($downloadStream)
            ->withHeader('Content-Type', 'application/vnd.debian.binary-package')
            ->withStatus(200);
    }
}
