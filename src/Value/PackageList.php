<?php

namespace ItsTreason\AptRepo\Value;

class PackageList
{
    private function __construct(
        private readonly string $path,
        private readonly string $content,
        private readonly int $size,
        private readonly string $md5sum,
        private readonly string $sha1,
        private readonly string $sha256,
    ) {}

    public static function fromValues(
        string $path,
        string $content,
        int $size,
        string $md5sum,
        string $sha1,
        string $sha256,
    ): static {
        return new self($path, $content, $size, $md5sum, $sha1, $sha256);
    }

    public static function fromDbRow(array $row): static
    {
        return new self(
            $row['path'],
            $row['content'],
            (int)$row['size'],
            $row['md5sum'],
            $row['sha1'],
            $row['sha256'],
        );
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getMd5sum(): string
    {
        return $this->md5sum;
    }

    public function getSha1(): string
    {
        return $this->sha1;
    }

    public function getSha256(): string
    {
        return $this->sha256;
    }
}
