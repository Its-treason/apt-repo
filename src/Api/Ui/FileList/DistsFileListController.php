<?php

namespace ItsTreason\AptRepo\Api\Ui\FileList;

use ItsTreason\AptRepo\Repository\SuitesRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig\Environment;

class DistsFileListController
{
    public function __construct(
        private readonly Environment $twig,
        private readonly SuitesRepository $suitesRepository,
    ) {}

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $files = [];

        $codenames = $this->suitesRepository->getAllCodenames();
        foreach ($codenames as $codename) {
            $files[] = ['name' => $codename . '/'];
        }

        $body = $this->twig->render('fileList.twig', [
            'showParentDir' => true,
            'path' => '/dists/',
            'files' => $files,
        ]);

        $response->getBody()->write($body);

        return $response->withStatus(200);
    }
}
