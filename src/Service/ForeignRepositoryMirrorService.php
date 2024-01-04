<?php

namespace ItsTreason\AptRepo\Service;

use GuzzleHttp\Client;
use ItsTreason\AptRepo\Value\PackageMetadata;

class ForeignRepositoryMirrorService
{
    public function __construct(
        private readonly Client $httpClient,
    ) {}

    /**
     * @return PackageMetadata[]
     */
    public function findForeignPackages(string $repoUrl, string $codename, string $component): array
    {
        $packagesUrl = sprintf('%s/dists/%s/%s/binary-amd64/Packages.gz', $repoUrl, $codename, $component);
        $response = $this->httpClient->get($packagesUrl);

        $rawPackage = gzdecode($response->getBody()->getContents());
        $rawPackageList = explode(PHP_EOL . PHP_EOL, $rawPackage);

        return [];
    }
}