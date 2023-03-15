<?php

namespace ItsTreason\AptRepo\Api\Ui\UploadPackage;

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
        } catch (Exception $exception) {
            var_dump($exception);

            return $response->withStatus(302)
                ->withHeader('Location', '/ui/upload?error=An error occurred');
        }

        $this->packageMetadataRepository->insertPackageMetadata($metadata);

        return $response->withStatus(302)
            ->withHeader('Location', '/ui/packages/' . $metadata->getFilename());
    }

    private function collectPackageMetadata(UploadedFile $file): PackageMetadata
    {
        $tempId = bin2hex(random_bytes(16));

        if (!mkdir($concurrentDirectory = sprintf('/tmp/%s', $tempId)) && !is_dir($concurrentDirectory)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }

        $tmpFilepath = sprintf('/tmp/%s/package.deb', $tempId);
        $file->moveTo($tmpFilepath);

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
}
