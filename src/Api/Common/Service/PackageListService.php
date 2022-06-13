<?php

namespace ItsTreason\AptRepo\Api\Common\Service;

use DateTime;
use ItsTreason\AptRepo\Api\Common\Repository\PackageListsRepository;
use ItsTreason\AptRepo\Api\Common\Repository\PackageMetadataRepository;
use ItsTreason\AptRepo\Api\Common\Repository\RepositoryInfoRepository;
use ItsTreason\AptRepo\Value\PackageList;
use ItsTreason\AptRepo\Value\PackageMetadata;

class PackageListService
{
    public function __construct(
        private readonly PackageMetadataRepository $packageMetadataRepository,
        private readonly PackageListsRepository $packageListsRepository,
        private readonly RepositoryInfoRepository $repositoryInfoRepository,
    ) {}

    public function updatePackageLists(): void
    {
        $allPackages = $this->packageMetadataRepository->getAllPackages();

        $groupedPackages = [];
        foreach ($allPackages as $package) {
            $groupedPackages[$package->getArch()][] = $package;
        }

        foreach ($groupedPackages as $arch => $packages) {
            $this->createPackageList($packages, $arch);
        }

        $this->repositoryInfoRepository->setValue(
            'Date',
            (new DateTime())->format('D, d M Y H:i:s T'),
        );
    }

    /**
     * @param PackageMetadata[] $packages
     * @param string $arch
     * @return void
     */
    private function createPackageList(array $packages, string $arch): void
    {
        $rawContent = '';
        foreach ($packages as $package) {
            $rawContent .= $package->getFullInfo();
        }

        $rawMd3sum = hash('md5', $rawContent);
        $rawSha1 = hash('sha1', $rawContent);
        $rawSha256 = hash('sha256', $rawContent);
        $rawSize = mb_strlen($rawContent, '8bit');
        $rawPath = sprintf('main/binary-%s/Packages', $arch);

        $rawPackageList = PackageList::fromValues($rawPath, $rawContent, $rawSize, $rawMd3sum, $rawSha1, $rawSha256);
        $this->packageListsRepository->updatePackageList($rawPackageList);

        $gzContent = gzencode($rawContent, 9);

        $gzMd3sum = hash('md5', $gzContent);
        $gzSha1 = hash('sha1', $gzContent);
        $gzSha256 = hash('sha256', $gzContent);
        $gzSize = mb_strlen($gzContent, '8bit');
        $gzPath = sprintf('main/binary-%s/Packages.gz', $arch);

        $gzPackageList = PackageList::fromValues($gzPath, $gzContent, $gzSize, $gzMd3sum, $gzSha1, $gzSha256);
        $this->packageListsRepository->updatePackageList($gzPackageList);
    }
}
