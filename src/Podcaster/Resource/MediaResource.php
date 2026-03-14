<?php

declare(strict_types=1);

namespace Podcaster\Resource;

use Podcaster\Dto\Media;
use Podcaster\Dto\Metadata;
use Podcaster\PodcasterClient;

class MediaResource extends AbstractResource
{
    /**
     * @return list<Media>
     */
    public function list(?string $sortBy = null, ?string $sortDir = null): array
    {
        $query = array_filter(['sortBy' => $sortBy, 'sortDir' => $sortDir], fn ($v) => $v !== null);
        $options = $query !== [] ? ['query' => $query] : [];

        $response = $this->client->get('/api/media', $options);

        $items = PodcasterClient::extractDataList($response);

        return array_map(
            fn (array $item) => Media::fromArray(PodcasterClient::extractAttributes($item)),
            $items,
        );
    }

    public function get(string $id): Media
    {
        $response = $this->client->get('/api/media/' . $id);

        return Media::fromArray(PodcasterClient::extractAttributes($response));
    }

    public function upload(string $filePath): Media
    {
        $response = $this->client->uploadFile('/api/media', $filePath);

        return Media::fromArray(PodcasterClient::extractAttributes($response));
    }

    public function delete(string $id): bool
    {
        $this->client->delete('/api/media/' . $id);

        return true;
    }

    public function getMetadata(string $id): Metadata
    {
        $response = $this->client->get('/api/media/' . $id . '/metadata');

        return Metadata::fromArray(PodcasterClient::extractAttributes($response));
    }

    /**
     * @param array<string, mixed> $data
     */
    public function updateMetadata(string $id, array $data): Metadata
    {
        $response = $this->client->putJson('/api/media/' . $id . '/metadata', $data);

        return Metadata::fromArray(PodcasterClient::extractAttributes($response));
    }
}
