<?php

namespace ItsTreason\AptRepo\Api\Common\Service;

use ItsTreason\AptRepo\Value\Id;
use Storj\Uplink\Project;
use Storj\Uplink\PsrStream\ReadStream;

class StorjFileService
{
    public function __construct(
        private Project $project,
    ) {}

    public function uploadFile(Id $id, string $filepath): void
    {
        $bucket = getenv('STORJ_BUCKET');
        $upload = $this->project->uploadObject($bucket, sprintf('%s.deb', $id->asString()));

        $resource = fopen($filepath, 'rb+');
        $upload->writeFromResource($resource);
        $upload->commit();
        fclose($resource);
    }

    public function downloadFile(Id $id): ReadStream
    {
        $bucket = getenv('STORJ_BUCKET');
        $download = $this->project->downloadObject($bucket, sprintf('%s.deb', $id->asString()));

        return $download->toPsrStream();
    }
}
