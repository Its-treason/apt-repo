<?php

namespace ItsTreason\AptRepo\Value;

use RuntimeException;

class Id
{
    public static function generate(): static
    {
        return new self(bin2hex(random_bytes(16)));
    }

    public static function fromString(string $id): static
    {
        return new self($id);
    }

    private function __construct(
        private readonly string $id,
    ) {
        $length = strlen($id);
        if ($length !== 32) {
            throw new RuntimeException(sprintf(
                'Expected Id with length "%s" but got "%s"',
                32,
                $length,
            ));
        }

        if (strlen(trim($id)) !== $length) {
            throw new RuntimeException('Id must not contain trimmable whitespaces ');
        }
    }

    public function asString(): string
    {
        return $this->id;
    }
}
