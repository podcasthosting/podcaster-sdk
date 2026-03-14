<?php

declare(strict_types=1);

namespace Podcaster\Dto;

use Podcaster\Dto\Episode\Itunes;

readonly class Episode implements \JsonSerializable
{
    public function __construct(
        public string $uuid,
        public string $title,
        public string $description = '',
        public string $author = '',
        public ?string $publishingDate = null,
        public ?string $publishingTime = null,
        public ?string $mediaId = null,
        public ?Itunes $itunes = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            uuid: (string) ($data['uuid'] ?? $data['id'] ?? ''),
            title: (string) ($data['title'] ?? ''),
            description: (string) ($data['description'] ?? ''),
            author: (string) ($data['author'] ?? ''),
            publishingDate: isset($data['publishingDate']) ? (string) $data['publishingDate'] : null,
            publishingTime: isset($data['publishingTime']) ? (string) $data['publishingTime'] : null,
            mediaId: isset($data['mediaId']) ? (string) $data['mediaId'] : null,
            itunes: isset($data['itunes']) ? Itunes::fromArray((array) $data['itunes']) : null,
        );
    }

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        return array_filter(get_object_vars($this), fn (mixed $v) => $v !== null);
    }
}
