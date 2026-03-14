<?php

declare(strict_types=1);

namespace Podcaster\Dto;

use Podcaster\Dto\Channel\Itunes;
use Podcaster\Dto\Channel\PodcastIndex;

readonly class Channel implements \JsonSerializable
{
    public function __construct(
        public string $uuid,
        public string $title,
        public string $link = '',
        public string $description = '',
        public string $author = '',
        public string $email = '',
        public string $copyright = '',
        public string $language = '',
        public string $category = '',
        public ?Itunes $itunes = null,
        public ?PodcastIndex $podcastIndex = null,
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
            link: (string) ($data['link'] ?? ''),
            description: (string) ($data['description'] ?? ''),
            author: (string) ($data['author'] ?? ''),
            email: (string) ($data['email'] ?? ''),
            copyright: (string) ($data['copyright'] ?? ''),
            language: (string) ($data['language'] ?? ''),
            category: (string) ($data['category'] ?? ''),
            itunes: isset($data['itunes']) ? Itunes::fromArray((array) $data['itunes']) : null,
            podcastIndex: isset($data['podcastIndex']) ? PodcastIndex::fromArray((array) $data['podcastIndex']) : null,
        );
    }

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        return array_filter(get_object_vars($this), fn (mixed $v) => $v !== null);
    }
}
