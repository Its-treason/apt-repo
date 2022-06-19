<?php

namespace ItsTreason\AptRepo\Api\FileList;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig\Environment;

class PoolComponentFileListController
{
    public function __construct(
        private readonly Environment $twig,
    ) {
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $files = [
            ['name' => 'main/'],
        ];

        $body = $this->twig->render('fileList.twig', [
            'showParentDir' => true,
            'path' => '/pool/',
            'files' => $files,
        ]);

        $response->getBody()->write($body);

        return $response->withStatus(200);
    }
}
