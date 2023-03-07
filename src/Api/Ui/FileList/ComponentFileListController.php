<?php

namespace ItsTreason\AptRepo\Api\Ui\FileList;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig\Environment;

class ComponentFileListController
{
    public function __construct(
        private readonly Environment $twig,
    ) {}

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $files = [
            ['name' => 'Release'],
            ['name' => 'Release.gpg'],
            ['name' => 'InRelease'],
            ['name' => 'main/'],
        ];

        $body = $this->twig->render('fileList.twig', [
            'showParentDir' => true,
            'path' => '/dists/stable/',
            'files' => $files,
        ]);

        $response->getBody()->write($body);

        return $response->withStatus(200);
    }
}
