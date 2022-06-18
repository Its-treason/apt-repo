<?php

namespace ItsTreason\AptRepo\Api\FileList;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig\Environment;

class RootFileListController
{
    public function __construct(
        private readonly Environment $twig,
    ) {}

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $files = [
            ['name' => 'dists/'],
            ['name' => 'pool/'],
        ];

        $body = $this->twig->render('fileList.twig', [
            'showParentDir' => false,
            'path' => '/',
            'files' => $files,
        ]);

        $response->getBody()->write($body);

        return $response->withStatus(200);
    }
}
