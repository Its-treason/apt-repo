<?php

namespace ItsTreason\AptRepo\Value;

class PackageList
{
    private function __construct(
        private readonly string $arch,
        private readonly string $type,
        private readonly string $codename,
        private readonly string $suite,
        private readonly string $content,
        private readonly int    $size,
        private readonly string $md5sum,
        private readonly string $sha1,
        private readonly string $sha256,
    ) {}

    public static function fromValues(
        string $arch,
        string $type,
        string $codename,
        string $suite,
        string $content,
        int $size,
        string $md5sum,
        string $sha1,
        string $sha256,
    ): static {
        return new self($arch, $type, $codename, $suite, $content, $size, $md5sum, $sha1, $sha256);
    }

    public static function fromDbRow(array $row): static
    {
        return new self(
            $row['arch'],
            $row['type'],
            $row['codename'],
            $row['suite'],
            $row['content'],
            (int)$row['size'],
            $row['md5sum'],
            $row['sha1'],
            $row['sha256'],
        );
    }

    public function getArch(): string
    {
        return $this->arch;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getCodename(): string
    {
        return $this->codename;
    }

    public function getSuite(): string
    {
        return $this->suite;
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
