<?php

declare(strict_types=1);

namespace Podcaster\Dto\Channel;

readonly class Itunes implements \JsonSerializable
{
    /**
     * @param list<string> $category
     */
    public function __construct(
        public ?string $subtitle = null,
        public ?string $explicit = null,
        public ?string $type = null,
        public ?string $complete = null,
        public array $category = [],
        public ?string $block = null,
        public ?string $newFeedUrl = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            subtitle: isset($data['subtitle']) ? (string) $data['subtitle'] : null,
            explicit: isset($data['explicit']) ? (string) $data['explicit'] : null,
            type: isset($data['type']) ? (string) $data['type'] : null,
            complete: isset($data['complete']) ? (string) $data['complete'] : null,
            category: isset($data['category']) ? (array) $data['category'] : [],
            block: isset($data['block']) ? (string) $data['block'] : null,
            newFeedUrl: isset($data['newFeedUrl']) ? (string) $data['newFeedUrl'] : null,
        );
    }

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        return array_filter(get_object_vars($this), fn (mixed $v) => $v !== null && $v !== []);
    }
}
