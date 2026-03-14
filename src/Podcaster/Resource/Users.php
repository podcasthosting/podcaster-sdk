<?php

declare(strict_types=1);

namespace Podcaster\Resource;

use Podcaster\Dto\User;
use Podcaster\PodcasterClient;

class Users extends AbstractResource
{
    public function me(): User
    {
        $response = $this->client->get('/api/user');

        return User::fromArray(PodcasterClient::extractAttributes($response));
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(int $id, array $data): User
    {
        $response = $this->client->putJson('/api/user/' . $id, $data);

        return User::fromArray(PodcasterClient::extractAttributes($response));
    }
}
