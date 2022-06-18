<?php

namespace ItsTreason\AptRepo\Api\FileList;

use ItsTreason\AptRepo\Api\Common\Repository\PackageMetadataRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig\Environment;

class DistsSuiteFileListController
{
    public function __construct(
        private readonly Environment $twig,
        private readonly PackageMetadataRepository $packageMetadataRepository,
    ) {}

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $arches = $this->packageMetadataRepository->getAllArches();

        $files = [];
        foreach ($arches as $arch) {
            $files[] = ['name' => sprintf('binary-%s/', $arch)];
        }

        $body = $this->twig->render('fileList.twig', [
            'showParentDir' => true,
            'path' => '/dists/stable/main/',
            'files' => $files,
        ]);

        $response->getBody()->write($body);

        return $response->withStatus(200);
    }
}
