<?php

namespace ItsTreason\AptRepo\Api\Ui\FileList;

use ItsTreason\AptRepo\Repository\PackageMetadataRepository;
use ItsTreason\AptRepo\Value\Suite;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteContext;
use Twig\Environment;

class ArchFileListController
{
    public function __construct(
        private readonly Environment $twig,
        private readonly PackageMetadataRepository $packageMetadataRepository,
    ) {}

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();

        $codename = $route?->getArgument('codename');
        $suite = $route?->getArgument('suite');
        $binaryArch = $route?->getArgument('arch');

        $suite = Suite::fromValues($codename, $suite);

        $archExists = $this->checkArchExists($binaryArch, $suite);
        if (!$archExists) {
            return $response->withStatus(404);
        }

        $files = [
            ['name' => 'Packages'],
            ['name' => 'Packages.gz'],
        ];

        $body = $this->twig->render('fileList.twig', [
            'showParentDir' => true,
            'path' => sprintf('/dists/%s/%s/%s/', $codename, $suite, $binaryArch),
            'files' => $files,
        ]);

        $response->getBody()->write($body);

        return $response->withStatus(200);
    }

    private function checkArchExists(string $binaryArch, Suite $suite): bool
    {
        // remove the 'binary-' prefix
        $prefix = 'binary-';
        if (substr($binaryArch, 0, strlen($prefix)) == $prefix) {
            $binaryArch = substr($binaryArch, strlen($prefix));
        }

        $arches = $this->packageMetadataRepository->getAllArches($suite);

        return in_array($binaryArch, $arches);
    }
}
