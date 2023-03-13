<?php

namespace ItsTreason\AptRepo\Api\Ui\FileList;

use ItsTreason\AptRepo\Repository\PackageMetadataRepository;
use ItsTreason\AptRepo\Value\Suite;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteContext;
use Twig\Environment;

class SuiteFileListController
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

        $suite = Suite::fromValues($codename, $suite);

        $arches = $this->packageMetadataRepository->getAllArches($suite);

        $files = [];
        foreach ($arches as $arch) {
            $files[] = ['name' => sprintf('binary-%s/', $arch)];
        }

        $body = $this->twig->render('fileList.twig', [
            'showParentDir' => true,
            'path' => sprintf('/dists/%s/%s/', $suite->getCodename(), $suite->getSuite()),
            'files' => $files,
        ]);

        $response->getBody()->write($body);

        return $response->withStatus(200);
    }
}
