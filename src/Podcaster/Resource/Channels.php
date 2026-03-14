<?php

declare(strict_types=1);

namespace Podcaster\Resource;

use Podcaster\Dto\Channel;
use Podcaster\PodcasterClient;

class Channels extends AbstractResource
{
    /**
     * @return list<Channel>
     */
    public function list(?int $page = null, ?int $size = null): array
    {
        $query = array_filter(['page' => $page, 'size' => $size], fn ($v) => $v !== null);
        $options = $query !== [] ? ['query' => $query] : [];

        $response = $this->client->get('/api/channels', $options);

        $items = PodcasterClient::extractDataList($response);

        return array_map(
            fn (array $item) => Channel::fromArray(PodcasterClient::extractAttributes($item)),
            $items,
        );
    }

    public function get(string $uuid): Channel
    {
        $response = $this->client->get('/api/channels/' . $uuid);

        return Channel::fromArray(PodcasterClient::extractAttributes($response));
    }

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): Channel
    {
        $response = $this->client->postJson('/api/channels', $data);

        return Channel::fromArray(PodcasterClient::extractAttributes($response));
    }

    public function delete(string $uuid): bool
    {
        $this->client->delete('/api/channels/' . $uuid);

        return true;
    }
}
