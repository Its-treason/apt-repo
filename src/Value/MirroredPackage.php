<?php

namespace ItsTreason\AptRepo\Value;

class MirroredPackage
{
    public function __construct(
        private readonly string $packageId,
        private readonly string $mirrorId,
        private readonly string $downloadUrl,
        private readonly string $name,
        private readonly string $version,
        private readonly string $arch,
        private readonly string $filename,
        private readonly string $fullInfo,
        private readonly DateTime $uploadDate,
    ) {}

    public static function create(
        string $packageId,
        string $mirrorId,
        string $downloadUrl,
    ): self {
        return new self($packageId, $mirrorId, $downloadUrl);
    }

    public function getPackageId(): string
    {
        return $this->packageId;
    }

    public function getMirrorId(): string
    {
        return $this->mirrorId;
    }

    public function getDownloadUrl(): string
    {
        return $this->downloadUrl;
    }
}