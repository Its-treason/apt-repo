<?php

namespace ItsTreason\AptRepo\Value;

use DateTime;

class PackageMetadata
{
    private function __construct(
        private readonly string $id,
        private readonly string $name,
        private readonly string $version,
        private readonly string $arch,
        private readonly string $filename,
        private readonly string $fullInfo,
        private readonly DateTime $uploadDate,
    ) {}

    public static function fromValues(
        string $id,
        string $name,
        string $version,
        string $arch,
        string $filename,
        string $fullInfo,
        DateTime $uploadDate,
    ): static {
        return new self($id, $name, $version, $arch, $filename, $fullInfo, $uploadDate);
    }

    public static function fromDbRow(
        array $row
    ): static {
        return new self(
            $row['package_id'],
            $row['name'],
            $row['version'],
            $row['arch'],
            $row['filename'],
            $row['fullinfo'],
            new DateTime($row['upload_date']),
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getArch(): string
    {
        return $this->arch;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getFullInfo(): string
    {
        return $this->fullInfo;
    }

    public function getUploadDate(): DateTime
    {
        return $this->uploadDate;
    }
}
