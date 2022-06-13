<?php

namespace ItsTreason\AptRepo\Api\UploadPackage;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig\Environment;

class UploadPackageFormController
{
    public function __construct(
        private Environment $twig,
    ) {}

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $template = $this->twig->load('uploadPackageForm.twig');

        $body = $template->render();

        $response->getBody()->write($body);

        return $response;
    }
}
