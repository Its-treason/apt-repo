<?php

namespace ItsTreason\AptRepo\Api\Ui\Suites;

use ItsTreason\AptRepo\Repository\SuitesRepository;
use ItsTreason\AptRepo\Value\Suite;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CreateSuiteController
{
    public function __construct(
        private readonly SuitesRepository $suitesRepository,
    ) {}

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $body = $request->getParsedBody();
        if (empty($body['codename']) || empty($body['suite'])) {
            $queryParams = http_build_query([ 'error' => 'Missing values' ]);
            return $response->withStatus(302)->withHeader('Location', '/ui/suites?' . $queryParams);
        }

        $suite = Suite::fromValues($body['codename'], $body['suite']);
        if ($this->suitesRepository->exists($suite)) {
            $queryParams = http_build_query([ 'error' => 'Suite already exists' ]);
            return $response->withStatus(302)->withHeader('Location', '/ui/suites?' . $queryParams);
        }

        $this->suitesRepository->create($suite);

        return $response->withStatus(302)->withHeader('Location', '/ui/suites?success=true');
    }
}
