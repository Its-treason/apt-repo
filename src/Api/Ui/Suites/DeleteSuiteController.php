<?php

namespace ItsTreason\AptRepo\Api\Ui\Suites;

use ItsTreason\AptRepo\Repository\SuitePackagesRepository;
use ItsTreason\AptRepo\Repository\SuitesRepository;
use ItsTreason\AptRepo\Value\Suite;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class DeleteSuiteController
{
    public function __construct(
        private readonly SuitesRepository $suitesRepository,
        private readonly SuitePackagesRepository $suitePackagesRepository,
    ) {}

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $body = $request->getParsedBody();
        if (empty($body['codename']) || empty($body['suite'])) {
            $queryParams = http_build_url([ 'error' => 'Missing values' ]);
            return $response->withStatus(302)->withHeader('Location', '/ui/suites?' . $queryParams);
        }

        $suite = Suite::fromValues($body['codename'], $body['suite']);

        $success = $this->suitesRepository->delete($suite);
        if (!$success) {
            $queryParams = http_build_url([ 'error' => 'Suite not found' ]);
            return $response->withStatus(302)->withHeader('Location', '/ui/suites?' . $queryParams);
        }

        $this->suitePackagesRepository->removeSuite($suite);

        return $response->withStatus(302)->withHeader('Location', '/ui/suites?success=true');
    }
}
