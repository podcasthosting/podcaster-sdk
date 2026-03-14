<?php

declare(strict_types=1);

namespace Podcaster\Dto;

readonly class Media implements \JsonSerializable
{
    public function __construct(
        public string $id,
        public string $title,
        public ?string $mimeType = null,
        public ?string $size = null,
        public ?string $duration = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: (string) ($data['id'] ?? ''),
            title: (string) ($data['title'] ?? ''),
            mimeType: isset($data['mimeType']) ? (string) $data['mimeType'] : null,
            size: isset($data['size']) ? (string) $data['size'] : null,
            duration: isset($data['duration']) ? (string) $data['duration'] : null,
        );
    }

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        return array_filter(get_object_vars($this), fn (mixed $v) => $v !== null);
    }
}
