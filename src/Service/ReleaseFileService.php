<?php

namespace ItsTreason\AptRepo\Service;

use ItsTreason\AptRepo\Repository\PackageListsRepository;
use ItsTreason\AptRepo\Repository\PackageMetadataRepository;
use ItsTreason\AptRepo\Repository\RepositoryInfoRepository;
use ItsTreason\AptRepo\Repository\SuitesRepository;
use Twig\Environment;

class ReleaseFileService
{
    public function __construct(
        private readonly PackageListsRepository    $packageListsRepository,
        private readonly RepositoryInfoRepository  $repositoryInfoRepository,
        private readonly PackageMetadataRepository $packageMetadataRepository,
        private readonly SuitesRepository          $suitesRepository,
        private readonly Environment               $twig,
    ) {}

    public function createReleaseFile(string $codename): string
    {
        $suites = $this->suitesRepository->getAllForCodename($codename);

        $md5Sum = '';
        $sha1 = '';
        $sha256 = '';

        foreach ($suites as $suite) {
            $packageLists = $this->packageListsRepository->getAllPackageListsForSuites($suite);
            foreach ($packageLists as $packageList) {
                $md5Sum .= sprintf(
                    ' %s %s %s%s',
                    $packageList->getMd5sum(),
                    $packageList->getSize(),
                    $packageList->buildPath(),
                    PHP_EOL,
                );
                $sha1 .= sprintf(
                    ' %s %s %s%s',
                    $packageList->getSha1(),
                    $packageList->getSize(),
                    $packageList->buildPath(),
                    PHP_EOL,
                );
                $sha256 .= sprintf(
                    ' %s %s %s%s',
                    $packageList->getSha256(),
                    $packageList->getSize(),
                    $packageList->buildPath(),
                    PHP_EOL,
                );
            }
        }

        $md5Sum = rtrim($md5Sum);
        $sha1 = rtrim($sha1);
        $sha256 = rtrim($sha256);

        $arches = $this->packageMetadataRepository->getAllArchesForCodename($codename);
        $arches = implode(' ', $arches);

        $origin = $this->repositoryInfoRepository->getValue('Origin');
        $label = $this->repositoryInfoRepository->getValue('Label');
        $version = $this->repositoryInfoRepository->getValue('Version');
        $date = $this->repositoryInfoRepository->getValue('Date');
        $description = $this->repositoryInfoRepository->getValue('Description');

        return $this->twig->render('Release.twig', [
            'origin' => $origin,
            'label' => $label,
            'version' => $version,
            'date' => $date,
            'description' => $description,
            'arch' => $arches,
            'md5sum' => $md5Sum,
            'sha1' => $sha1,
            'sha256' => $sha256,
        ]);
    }
}
