<?php

namespace ItsTreason\AptRepo\Api\Ui\FileList;

use ItsTreason\AptRepo\Repository\SuitesRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteContext;
use Twig\Environment;

class ComponentFileListController
{
    public function __construct(
        private readonly Environment $twig,
        private readonly SuitesRepository $suitesRepository,
    ) {}

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();

        $codename = $route?->getArgument('codename');

        $suites = $this->suitesRepository->getAllForCodename($codename);

        $files = [
            ['name' => 'Release'],
            ['name' => 'Release.gpg'],
            ['name' => 'InRelease'],
        ];
        foreach ($suites as $suite) {
            $files[] = ['name' => $suite->getSuite() . '/'];
        }

        $body = $this->twig->render('fileList.twig', [
            'showParentDir' => true,
            'path' => '/dists/stable/',
            'files' => $files,
        ]);

        $response->getBody()->write($body);

        return $response->withStatus(200);
    }
}
