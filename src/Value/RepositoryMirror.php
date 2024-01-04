<?php

namespace ItsTreason\AptRepo\Value;

class RepositoryMirror
{
    public function __construct(
        private readonly string $id,
        private readonly string $repoUrl,
        private readonly string $codename,
        private readonly string $component,
        private readonly string $arch,
        private readonly string $targetCodename,
        private readonly string $targetComponent,
    ) {}

    public static function create(string $id, string $repoUrl, string $codename, string $component, string $arch): self
    {
        return new self(
            $id,
            $repoUrl,
            $codename,
            $component,
            $arch,
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getRepoUrl(): string
    {
        return $this->repoUrl;
    }

    public function getCodename(): string
    {
        return $this->codename;
    }

    public function getComponent(): string
    {
        return $this->component;
    }

    public function getArch(): string
    {
        return $this->arch;
    }
}