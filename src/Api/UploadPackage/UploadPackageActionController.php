<?php

namespace ItsTreason\AptRepo\Api\UploadPackage;

use DateTime;
use Exception;
use GuzzleHttp\Psr7\UploadedFile;
use ItsTreason\AptRepo\FileStorage\FileStorageInterface;
use ItsTreason\AptRepo\Repository\PackageMetadataRepository;
use ItsTreason\AptRepo\Service\PackageListService;
use ItsTreason\AptRepo\Value\PackageMetadata;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;

class UploadPackageActionController
{
    public function __construct(
        private readonly FileStorageInterface      $fileStorage,
        private readonly PackageMetadataRepository $packageMetadataRepository,
        private readonly PackageListService        $packageListService,
    ) {}

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $files = $request->getUploadedFiles();

        if (!isset($files['package'])) {
            return $response->withStatus(302)
                ->withHeader('Location', '/ui/upload?error=An error occurred');
        }

        /** @var UploadedFile $file */
        $file = $files['package'];

        try {
            $metadata = $this->collectPackageMetadata($file);
        } catch (Exception) {
            return $response->withStatus(302)
                ->withHeader('Location', '/ui/upload?error=An error occurred');
        }

        $this->packageMetadataRepository->insertPackageMetadata($metadata);
        $this->packageListService->updatePackageLists();

        return $response->withStatus(302)
            ->withHeader('Location', '/ui/upload?success=true');
    }

    private function collectPackageMetadata(UploadedFile $file): PackageMetadata
    {
        $id = bin2hex(random_bytes(16));

        if (!mkdir($concurrentDirectory = sprintf('/tmp/%s', $id)) && !is_dir($concurrentDirectory)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }

        $tmpFilepath = sprintf('/tmp/%s/package.deb', $id);
        $file->moveTo($tmpFilepath);

        $fullInfo = shell_exec(sprintf('dpkg-scanpackages "%s"', $tmpFilepath));

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

        $filename = sprintf('pool/main/%s_%s_%s.deb', $name, $version, $arch);

        $fullInfo = str_replace(
            $tmpFilepath,
            $filename,
            $fullInfo,
        );

        $this->fileStorage->uploadFile($id, $tmpFilepath);
        unlink($tmpFilepath);

        $uploadDate = new DateTime();

        return PackageMetadata::fromValues($id, $name, $version, $arch, $filename, $fullInfo, $uploadDate);
    }
}
