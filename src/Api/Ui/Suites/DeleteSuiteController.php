<?php

namespace ItsTreason\AptRepo\Api\Ui\Suites;

use ItsTreason\AptRepo\Repository\SuitesRepository;
use ItsTreason\AptRepo\Value\Suite;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class DeleteSuiteController
{
    public function __construct(
        private readonly SuitesRepository $suitesRepository,
    ) {}

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $body = $request->getParsedBody();
        if (empty($body['codename']) || empty($body['suite'])) {
            return $response->withStatus(302)->withHeader('Location', '/ui/suites?error=Missing values');
        }

        $suite = Suite::fromValues($body['codename'], $body['suite']);

        $this->suitesRepository->delete($suite);

        return $response->withStatus(302)->withHeader('Location', '/ui/suites?success=true');
    }
}
