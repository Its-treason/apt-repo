<?php

namespace ItsTreason\AptRepo\FileStorage;

use Psr\Container\ContainerInterface;
use RuntimeException;

class FileStorageFactory
{
    public function __invoke(ContainerInterface $container): FileStorageInterface
    {
        $storageType = getenv('STORAGE');
        return match ($storageType) {
            FilesystemFileStorage::STORAGE_TYPE => $container->get(FilesystemFileStorage::class),
            StorjFileStorage::STORAGE_TYPE => $container->get(StorjFileStorage::class),
            default => throw new RuntimeException(sprintf('Unknown Storage type "%s" encountered', $storageType)),
        };
    }
}
