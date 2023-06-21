<?php

namespace ItsTreason\AptRepo\Api\Ui\PackageList;

use ItsTreason\AptRepo\Repository\PackageMetadataRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig\Environment;

class PackageListController
{
    public function __construct(
        private readonly Environment $twig,
        private readonly PackageMetadataRepository $packageMetadataRepository,
    ) {}

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {;
        $search = $request->getQueryParams()['search'] ?? '';
        $groupPackages = isset($request->getQueryParams()['groupPackages']);
        $sort = $request->getQueryParams()['sort'] ?? 'name';
        if ($sort !== 'name' && $sort !== 'date') {
            $sort = 'name';
        }

        if ($groupPackages) {
            $packages = $this->packageMetadataRepository->getAllPackageGroupedByName($search, $sort);
        } else {
            $packages = $this->packageMetadataRepository->getAllPackages($search, $sort);
        }

        $body = $this->twig->render('packageList.twig', [
            'packages' => $packages,
            'groupPackages' => $groupPackages,
            'search' => $search,
            'sort' => $sort,
        ]);

        $response->getBody()->write($body);

        return $response;
    }
}
