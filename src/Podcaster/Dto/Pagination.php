<?php

declare(strict_types=1);

namespace Podcaster\Dto;

readonly class Pagination implements \JsonSerializable
{
    public function __construct(
        public int $currentPage,
        public int $lastPage,
        public int $perPage,
        public int $total,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            currentPage: (int) ($data['current_page'] ?? $data['currentPage'] ?? 1),
            lastPage: (int) ($data['last_page'] ?? $data['lastPage'] ?? 1),
            perPage: (int) ($data['per_page'] ?? $data['perPage'] ?? 15),
            total: (int) ($data['total'] ?? 0),
        );
    }

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}
