<?php

namespace ItsTreason\AptRepo\Service;

use DateTime;
use ItsTreason\AptRepo\Repository\PackageListsRepository;
use ItsTreason\AptRepo\Repository\PackageMetadataRepository;
use ItsTreason\AptRepo\Repository\RepositoryInfoRepository;
use ItsTreason\AptRepo\Value\PackageList;
use ItsTreason\AptRepo\Value\PackageMetadata;
use ItsTreason\AptRepo\Value\Suite;
use Monolog\Logger;

class PackageListService
{
    public function __construct(
        private readonly PackageMetadataRepository $packageMetadataRepository,
        private readonly PackageListsRepository $packageListsRepository,
        private readonly RepositoryInfoRepository $repositoryInfoRepository,
        private readonly Logger $logger,
    ) {}

    public function updatePackageLists(Suite $suite): void
    {
        $allPackages = $this->packageMetadataRepository->getAllPackagesForSuite($suite);

        $this->logger->info('Updating package list', [
            'codename' => $suite->getCodename(),
            'component' => $suite->getSuite(),
            'packageCount' => count($allPackages),
        ]);

        $groupedPackages = [];
        foreach ($allPackages as $package) {
            $groupedPackages[$package->getArch()][] = $package;
        }

        foreach ($groupedPackages as $arch => $packages) {
            $this->createPackageList($packages, $arch, $suite);
        }

        $this->repositoryInfoRepository->setValue(
            'Date',
            (new DateTime())->format('D, d M Y H:i:s T'),
        );
    }

    /**
     * @param PackageMetadata[] $packages
     * @param string $arch
     * @param Suite $suite
     * @return void
     */
    private function createPackageList(array $packages, string $arch, Suite $suite): void
    {
        $rawContent = '';
        foreach ($packages as $package) {
            $rawContent .= $package->getFullInfo();
        }

        $rawMd3sum = hash('md5', $rawContent);
        $rawSha1 = hash('sha1', $rawContent);
        $rawSha256 = hash('sha256', $rawContent);
        $rawSize = mb_strlen($rawContent, '8bit');

        $rawPackageList = PackageList::fromValues(
            $arch, 'Packages', $suite->getCodename(), $suite->getSuite(), $rawContent, $rawSize, $rawMd3sum, $rawSha1, $rawSha256
        );
        $this->packageListsRepository->updatePackageList($rawPackageList);

        $gzContent = gzencode($rawContent, 9);

        $gzMd3sum = hash('md5', $gzContent);
        $gzSha1 = hash('sha1', $gzContent);
        $gzSha256 = hash('sha256', $gzContent);
        $gzSize = mb_strlen($gzContent, '8bit');

        $gzPackageList = PackageList::fromValues(
            $arch, 'Packages.gz', $suite->getCodename(), $suite->getSuite(), $gzContent, $gzSize, $gzMd3sum, $gzSha1, $gzSha256,
        );
        $this->packageListsRepository->updatePackageList($gzPackageList);
    }
}
