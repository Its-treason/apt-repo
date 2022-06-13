<?php

namespace ItsTreason\AptRepo\Api\Release\Service;

use DateTime;
use ItsTreason\AptRepo\Api\Common\Repository\PackageListsRepository;
use ItsTreason\AptRepo\Api\Common\Repository\PackageMetadataRepository;
use ItsTreason\AptRepo\Api\Common\Repository\RepositoryInfoRepository;
use Twig\Environment;

class ReleaseFileService
{
    public function __construct(
        private readonly PackageListsRepository $packageListsRepository,
        private readonly RepositoryInfoRepository $repositoryInfoRepository,
        private readonly PackageMetadataRepository $packageMetadataRepository,
        private readonly Environment $twig,
    ) {}

    public function createReleaseFile(): string
    {
        $packageLists = $this->packageListsRepository->getAllPackageLists();

        $md5Sum = '';
        $sha1 = '';
        $sha256 = '';
        foreach ($packageLists as $packageList) {
            $md5Sum .= sprintf(
                ' %s %s %s%s',
                $packageList->getMd5sum(),
                $packageList->getSize(),
                $packageList->getPath(),
                PHP_EOL,
            );
            $sha1 .= sprintf(
                ' %s %s %s%s',
                $packageList->getSha1(),
                $packageList->getSize(),
                $packageList->getPath(),
                PHP_EOL,
            );
            $sha256 .= sprintf(
                ' %s %s %s%s',
                $packageList->getSha256(),
                $packageList->getSize(),
                $packageList->getPath(),
                PHP_EOL,
            );
        }

        $md5Sum = rtrim($md5Sum);
        $sha1 = rtrim($sha1);
        $sha256 = rtrim($sha256);

        $arches = $this->packageMetadataRepository->getAllArches();
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
