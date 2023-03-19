<?php

namespace ItsTreason\AptRepo\FileStorage;

use GuzzleHttp\Psr7\Stream;
use Psr\Http\Message\StreamInterface;
use RuntimeException;

class FilesystemFileStorage implements FileStorageInterface
{
    public const STORAGE_TYPE = 'filesystem';

    public function uploadFile(string $id, string $filepath): void
    {
        $storageLocation = getenv('STORAGE_LOCATION');
        $target = sprintf('%s/%s.deb', $storageLocation, $id);
        $success = copy($filepath, $target);

        if (!$success) {
            throw new RuntimeException(sprintf('Could not copy deb file from "%s" to "%s"', $filepath, $target));
        }
    }

    public function downloadFile(string $id): StreamInterface
    {
        $storageLocation = getenv('STORAGE_LOCATION');

        $resource = fopen(sprintf('%s/%s.deb', $storageLocation, $id), 'r');

        return new Stream($resource);
    }

    public function deleteFile(string $id): void
    {
        $storageLocation = getenv('STORAGE_LOCATION');
        $target = sprintf('%s/%s.deb', $storageLocation, $id);

        unlink($target);
    }
}
