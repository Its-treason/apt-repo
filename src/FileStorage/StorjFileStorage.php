<?php

namespace ItsTreason\AptRepo\FileStorage;

use Storj\Uplink\Project;
use Storj\Uplink\PsrStream\ReadStream;

class StorjFileStorage implements FileStorageInterface
{
    public const STORAGE_TYPE = 'storj';

    public function __construct(
        private readonly Project $project,
    ) {}

    public function uploadFile(string $id, string $filepath): void
    {
        $bucket = getenv('STORJ_BUCKET');
        $upload = $this->project->uploadObject($bucket, sprintf('%s.deb', $id));

        $resource = fopen($filepath, 'rb+');
        $upload->writeFromResource($resource);
        $upload->commit();
        fclose($resource);
    }

    public function downloadFile(string $id): ReadStream
    {
        $bucket = getenv('STORJ_BUCKET');
        $download = $this->project->downloadObject($bucket, sprintf('%s.deb', $id));

        return $download->toPsrStream();
    }

    public function deleteFile(string $id): void
    {
        $bucket = getenv('STORJ_BUCKET');

        $this->project->deleteObject($bucket, $id . '.deb');
    }
}
