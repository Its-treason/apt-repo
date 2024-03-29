<?php

namespace ItsTreason\AptRepo\Service;

use DateTime;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use ItsTreason\AptRepo\FileStorage\FileStorageInterface;
use ItsTreason\AptRepo\Repository\PackageMetadataRepository;
use ItsTreason\AptRepo\Value\GitHubRelease;
use ItsTreason\AptRepo\Value\PackageMetadata;
use GuzzleHttp\Psr7\UploadedFile;
use RuntimeException;

class PackageParseService
{
    function __construct(
        private readonly FileStorageInterface $fileStorage,
        private readonly Client $httpClient,
        private readonly PackageMetadataRepository $packageMetadataRepository,
    ) {}

    public function collectPackageMetadata(UploadedFile|string $file): PackageMetadata
    {
        $tempId = bin2hex(random_bytes(16));

        if (!mkdir($concurrentDirectory = sprintf('/tmp/%s', $tempId)) && !is_dir($concurrentDirectory)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }

        $tmpFilepath = sprintf('/tmp/%s/package.deb', $tempId);
        if ($file instanceof UploadedFile) {
            $file->moveTo($tmpFilepath);
        } else {
            rename($file, $tmpFilepath);
        }

        $fullInfo = shell_exec(sprintf('dpkg-scanpackages "%s"', $tmpFilepath));

        // TODO: Create parser for this
        $name = null;
        $version = null;
        $arch = null;

        $lines = explode(PHP_EOL, $fullInfo);

        foreach ($lines as $line) {
            $splittedLine = explode(' ', $line);
            if (!isset($splittedLine[0], $splittedLine[1])) {
                continue;
            }

            switch ($splittedLine[0]) {
                case 'Package:':
                    $name = $splittedLine[1];
                    break;
                case 'Architecture:':
                    $arch = $splittedLine[1];
                    break;
                case 'Version:':
                    $version = $splittedLine[1];
                    break;
            }
        }

        if ($name === null || $version === null || $arch === null) {
            throw new RuntimeException('Metadata missing');
        }

        $filename = sprintf('%s_%s_%s.deb', $name, $version, $arch);

        // Replace the Filepath to the correct when, used in the Packages file
        $fullInfo = str_replace(
            $tmpFilepath,
            'pool/main/' . $filename,
            $fullInfo,
        );

        $packageId = hash('md5', $name . $version . $arch);

        $this->fileStorage->uploadFile($packageId, $tmpFilepath);
        unlink($tmpFilepath);

        $uploadDate = new DateTime();

        return PackageMetadata::fromValues($packageId, $name, $version, $arch, $filename, $fullInfo, $uploadDate);
    }

    public function addPackagesFromRelease(GitHubRelease $release): void
    {
        foreach ($release->getFiles() as $downloadUrl) {
            try {
                $file = sprintf('/tmp/%s.deb', bin2hex(random_bytes(12)));
                $this->httpClient->get($downloadUrl, [
                    RequestOptions::SINK => $file,
                    RequestOptions::ALLOW_REDIRECTS => true,
                ]);

                $metadata = $this->collectPackageMetadata($file);
                if (!$this->packageMetadataRepository->getPackageByFilename($metadata->getFilename())) {
                    $this->packageMetadataRepository->insertPackageMetadata($metadata);
                }
            } catch (Exception $exception) {
                error_log($exception);
            }
        }
    }
}
