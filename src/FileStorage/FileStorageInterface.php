<?php

namespace ItsTreason\AptRepo\FileStorage;

use Psr\Http\Message\StreamInterface;

interface FileStorageInterface
{
    public function uploadFile(string $id, string $filepath): void;

    public function downloadFile(string $id): StreamInterface;

    public function deleteFile(string $id): void;
}
