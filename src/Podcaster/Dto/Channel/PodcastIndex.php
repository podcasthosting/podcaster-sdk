<?php

declare(strict_types=1);

namespace Podcaster\Dto\Channel;

readonly class PodcastIndex implements \JsonSerializable
{
    public function __construct(
        public ?string $locked = null,
        public ?string $funding = null,
        public ?string $fundingUrl = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            locked: isset($data['locked']) ? (string) $data['locked'] : null,
            funding: isset($data['funding']) ? (string) $data['funding'] : null,
            fundingUrl: isset($data['fundingUrl']) ? (string) $data['fundingUrl'] : null,
        );
    }

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        return array_filter(get_object_vars($this), fn (mixed $v) => $v !== null);
    }
}
