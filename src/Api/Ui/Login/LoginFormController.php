<?php

namespace ItsTreason\AptRepo\Api\Ui\Login;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig\Environment;

class LoginFormController
{
    public function __construct(
        private readonly Environment $twig,
    ) {
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $template = $this->twig->load('login.twig');

        $body = $template->render();

        $response->getBody()->write($body);

        return $response;
    }
}
