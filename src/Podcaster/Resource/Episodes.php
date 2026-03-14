<?php

declare(strict_types=1);

namespace Podcaster\Resource;

use Podcaster\Dto\Episode;
use Podcaster\PodcasterClient;

class Episodes extends AbstractResource
{
    /**
     * @return list<Episode>
     */
    public function list(string $channelId, ?int $page = null, ?int $size = null): array
    {
        $query = array_filter(
            ['channel' => $channelId, 'page' => $page, 'size' => $size],
            fn ($v) => $v !== null,
        );

        $response = $this->client->get('/api/shows', ['query' => $query]);

        $items = PodcasterClient::extractDataList($response);

        return array_map(
            fn (array $item) => Episode::fromArray(PodcasterClient::extractAttributes($item)),
            $items,
        );
    }

    public function get(string $uuid): Episode
    {
        $response = $this->client->get('/api/shows/' . $uuid);

        return Episode::fromArray(PodcasterClient::extractAttributes($response));
    }

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): Episode
    {
        $response = $this->client->postJson('/api/shows', $data);

        return Episode::fromArray(PodcasterClient::extractAttributes($response));
    }

    public function delete(string $uuid): bool
    {
        $this->client->delete('/api/shows/' . $uuid);

        return true;
    }

    public function copy(string $uuid, ?string $targetChannelUuid = null): Episode
    {
        $data = $targetChannelUuid !== null ? ['channel' => $targetChannelUuid] : [];

        $response = $this->client->postJson('/api/show/' . $uuid . '/copy', $data);

        return Episode::fromArray(PodcasterClient::extractAttributes($response));
    }
}
