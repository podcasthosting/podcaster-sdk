<?php

declare(strict_types=1);

namespace Podcaster\Dto;

readonly class Metadata implements \JsonSerializable
{
    /**
     * @param array<string, mixed> $attributes
     */
    public function __construct(
        public string $id,
        public array $attributes = [],
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $id = (string) ($data['id'] ?? '');
        unset($data['id']);

        return new self(
            id: $id,
            attributes: $data,
        );
    }

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        return ['id' => $this->id, ...$this->attributes];
    }
}
