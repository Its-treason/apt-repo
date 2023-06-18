<?php

namespace ItsTreason\AptRepo\Value;

class GitHubRelease
{
    /**
    * @param $files string[]
    */
    private function __construct(
        private readonly string $releaseName,
        private readonly array $files,
    ) {}

    /**
    * @param $files string[]
    */
    public static function create(string $releaseName, array $files): self
    {
        return new self($releaseName, $files);
    }

    public function getReleaseName(): string
    {
        return $this->releaseName;
    }

    public function getFiles(): array
    {
        return $this->files;
    }
}
