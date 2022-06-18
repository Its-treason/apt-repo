<?php

namespace ItsTreason\AptRepo\Api\FileList;

use ItsTreason\AptRepo\Api\Common\Repository\PackageMetadataRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig\Environment;

class PoolFileListController
{
    public function __construct(
        private readonly Environment $twig,
        private readonly PackageMetadataRepository $packageMetadataRepository,
    ) {}

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $packages = $this->packageMetadataRepository->getAllPackages();

        $files = [];
        foreach ($packages as $package) {
            $files[] = [
                'name' => sprintf(
                    '%s_%s_%s.deb',
                    $package->getName(),
                    $package->getVersion(),
                    $package->getArch(),
                ),
            ];
        }

        $body = $this->twig->render('fileList.twig', [
            'showParentDir' => true,
            'path' => '/pool/main/',
            'files' => $files,
        ]);

        $response->getBody()->write($body);

        return $response->withStatus(200);
    }
}
