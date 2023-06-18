<?php

namespace ItsTreason\AptRepo\Api\PackageDownload;

use ItsTreason\AptRepo\FileStorage\FileStorageInterface;
use ItsTreason\AptRepo\Repository\PackageMetadataRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteContext;

class PackageDownloadController
{
    public function __construct(
        private readonly FileStorageInterface      $fileStorage,
        private readonly PackageMetadataRepository $packageMetadataRepository,
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

        $downloadStream = $this->fileStorage->downloadFile($packageMetadata->getId());
        return $response->withBody($downloadStream)
            ->withHeader('Content-Type', 'application/vnd.debian.binary-package')
            ->withHeader('Cache-Control', 'public, max-age=86400')
            ->withStatus(200);
    }
}
