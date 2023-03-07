<?php

namespace ItsTreason\AptRepo\Api\RepositoryInfo;

use ItsTreason\AptRepo\Repository\RepositoryInfoRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig\Environment;

class RepositoryInfoController
{
    public function __construct(
        private Environment $twig,
        private readonly RepositoryInfoRepository $repositoryInfoRepository,
    ) {
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $port = '';
        if ($request->getUri()->getPort() !== null) {
            $port = sprintf(':%s', $request->getUri()->getPort());
        }

        $host = sprintf(
            '%s://%s%s',
            $request->getUri()->getScheme(),
            $request->getUri()->getHost(),
            $port,
        );

        $repoName = $this->repositoryInfoRepository->getValue('Origin');
        $repoNameEscaped = str_replace(' ', '_', strtolower($repoName));
        $repoDescription = $this->repositoryInfoRepository->getValue('Description');

        $body = $this->twig->render('repositoryInfo.twig', [
            'repoName' => $repoName,
            'repoNameEscaped' => $repoNameEscaped,
            'repoDescription' => $repoDescription,
            'host' => $host,
        ]);

        $response->getBody()->write($body);

        return $response;
    }
}
