<?php

namespace ItsTreason\AptRepo\FileStorage;

use GuzzleHttp\Psr7\Stream;
use Psr\Http\Message\StreamInterface;

class FilesystemFileStorage implements FileStorageInterface
{
    public const STORAGE_TYPE = 'filesystem';

    public function uploadFile(string $id, string $filepath): void
    {
        $storageLocation = getenv('STORAGE_LOCATION');
        copy($filepath, sprintf('%s/%s.deb', $storageLocation, $id));
    }

    public function downloadFile(string $id): StreamInterface
    {
        $storageLocation = getenv('STORAGE_LOCATION');

        $resource = fopen(sprintf('%s/%s.deb', $storageLocation, $id), 'r');

        return new Stream($resource);
    }
}
