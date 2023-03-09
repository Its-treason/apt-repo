<?php

namespace ItsTreason\AptRepo\Value;

class Suite
{
    public function __construct(
        private readonly string $codename,
        private readonly string $suite,
    ) {}

    public static function fromValues(string $codename, string $suite): self
    {
        return new self($codename, $suite);
    }

    public function getCodename(): string
    {
        return $this->codename;
    }

    public function getSuite(): string
    {
        return $this->suite;
    }
}
