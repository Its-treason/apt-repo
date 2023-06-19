<?php

namespace ItsTreason\AptRepo\Value;

class GitHubSubscription
{
    private function __construct(
        private readonly string $owner,
        private readonly string $name,
        private readonly ?string $lastRelease,
    ) {}

    public static function create(string $owner, string $name, ?string $lastRelease): self
    {
        return new self($owner, $name, $lastRelease);
    }

    public static function fromDbRow(array $row): self
    {
        return new self($row['owner'], $row['name'], $row['last_release']);
    }

    public function getOwner(): string
    {
        return $this->owner;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLastRelease(): ?string
    {
        return $this->lastRelease;
    }

    public function withNewLastRelease(string $lastRelease): self
    {
        return new self($this->owner, $this->name, $lastRelease);
    }
}
