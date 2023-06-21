<?php

namespace ItsTreason\AptRepo\Value;

use DateTime;

class GroupedPackageMetadata
{
    private function __construct(
        private readonly string $name,
        private readonly int $totalPackages,
        private readonly DateTime $uploadDate,
    ) {}

    public static function fromValues(
        string $name,
        int $totalPackages,
        DateTime $uploadDate,
    ): static {
        return new self($name, $totalPackages, $uploadDate);
    }

    public static function fromDbRow(
        array $row,
    ): static {
        return new self(
            $row['name'],
            $row['total_packages'],
            new DateTime($row['upload_date']),
        );
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTotalPackages(): int
    {
        return $this->totalPackages;
    }

    public function getUploadDate(): DateTime
    {
        return $this->uploadDate;
    }
}
