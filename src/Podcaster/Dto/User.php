<?php

declare(strict_types=1);

namespace Podcaster\Dto;

readonly class User implements \JsonSerializable
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) ($data['id'] ?? $data['uuid'] ?? 0),
            name: (string) ($data['name'] ?? ''),
            email: (string) ($data['email'] ?? ''),
        );
    }

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}
