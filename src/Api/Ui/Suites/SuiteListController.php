<?php

namespace ItsTreason\AptRepo\Api\Ui\Suites;

use ItsTreason\AptRepo\Repository\SuitesRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig\Environment;

class SuiteListController
{
    public function __construct(
        private readonly SuitesRepository $suitesRepository,
        private readonly Environment      $twig,
    ) {}

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $suites = $this->suitesRepository->getAll();

        $loggedIn = isset($request->getCookieParams()['apikey']);

        $body = $this->twig->render('suites.twig', [
            'suites' => $suites,
            'loggedIn' => $loggedIn,
        ]);

        $response->getBody()->write($body);

        return $response->withStatus(200);
    }
}
