<?php

namespace ItsTreason\AptRepo\Api\Ui\UploadPackage;

use Exception;
use GuzzleHttp\Psr7\UploadedFile;
use ItsTreason\AptRepo\Repository\PackageMetadataRepository;
use Psr\Http\Message\ResponseInterface;
use ItsTreason\AptRepo\Service\PackageParseService;
use Psr\Http\Message\ServerRequestInterface;

class UploadPackageActionController
{
    public function __construct(
        private readonly PackageMetadataRepository $packageMetadataRepository,
        private readonly PackageParseService $packageParser,
    ) {}

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $files = $request->getUploadedFiles();
        if (!isset($files['package'])) {
            return $response->withStatus(302)
                ->withHeader('Location', '/ui/upload?error=No files uploaded');
        }

        /** @var UploadedFile $file */
        $file = $files['package'];

        try {
            //$metadata = $this->packageParser->
        } catch (Exception $exception) {
            $message = urlencode('Failed to parse package: ' . $exception->getMessage());
            return $response->withStatus(302)
                ->withHeader('Location', '/ui/upload?error=' . $message);
        }

        $this->packageMetadataRepository->insertPackageMetadata($metadata);

        return $response->withStatus(302)
            ->withHeader('Location', '/ui/packages/' . $metadata->getFilename());
    }

}
