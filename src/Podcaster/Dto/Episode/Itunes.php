<?php

declare(strict_types=1);

namespace Podcaster\Dto\Episode;

readonly class Itunes implements \JsonSerializable
{
    public function __construct(
        public ?string $subtitle = null,
        public ?string $summary = null,
        public ?string $explicit = null,
        public ?string $episodeType = null,
        public ?int $season = null,
        public ?int $episode = null,
        public ?string $block = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            subtitle: isset($data['subtitle']) ? (string) $data['subtitle'] : null,
            summary: isset($data['summary']) ? (string) $data['summary'] : null,
            explicit: isset($data['explicit']) ? (string) $data['explicit'] : null,
            episodeType: isset($data['episodeType']) ? (string) $data['episodeType'] : null,
            season: isset($data['season']) ? (int) $data['season'] : null,
            episode: isset($data['episode']) ? (int) $data['episode'] : null,
            block: isset($data['block']) ? (string) $data['block'] : null,
        );
    }

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        return array_filter(get_object_vars($this), fn (mixed $v) => $v !== null);
    }
}
