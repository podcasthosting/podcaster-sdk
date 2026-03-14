<?php

declare(strict_types=1);

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Podcaster\Dto\Channel;
use Podcaster\Dto\Episode;
use Podcaster\Dto\Media;
use Podcaster\Dto\User;
use Podcaster\Exceptions\NotAuthorizedException;
use Podcaster\Exceptions\UnexpectedResponseException;
use Podcaster\PodcasterClient;

function createClientWithMock(array $responses): PodcasterClient
{
    $mock = new MockHandler($responses);
    $handlerStack = HandlerStack::create($mock);
    $httpClient = new Client(['handler' => $handlerStack]);

    return new PodcasterClient('test-token', time() + 3600, $httpClient);
}

test('Client version is 2.0.0', function () {
    expect(PodcasterClient::VERSION)->toBe('2.0.0');
});

test('Client base URL points to app.podcaster.de', function () {
    expect(PodcasterClient::API_BASEURL)->toBe('app.podcaster.de');
});

test('Client can fetch channels list', function () {
    $client = createClientWithMock([
        new Response(200, [], json_encode([
            'data' => [
                [
                    'id' => 'ch-1',
                    'attributes' => [
                        'title' => 'Podcast 1',
                        'description' => 'Description 1',
                    ],
                ],
                [
                    'id' => 'ch-2',
                    'attributes' => [
                        'title' => 'Podcast 2',
                        'description' => 'Description 2',
                    ],
                ],
            ],
        ])),
    ]);

    $channels = $client->channels()->list();

    expect($channels)->toHaveCount(2)
        ->and($channels[0])->toBeInstanceOf(Channel::class)
        ->and($channels[0]->title)->toBe('Podcast 1')
        ->and($channels[1]->title)->toBe('Podcast 2');
});

test('Client can fetch single channel', function () {
    $client = createClientWithMock([
        new Response(200, [], json_encode([
            'data' => [
                'id' => 'ch-1',
                'attributes' => [
                    'title' => 'My Podcast',
                    'description' => 'A great podcast',
                    'language' => 'de',
                ],
            ],
        ])),
    ]);

    $channel = $client->channels()->get('ch-1');

    expect($channel)->toBeInstanceOf(Channel::class)
        ->and($channel->uuid)->toBe('ch-1')
        ->and($channel->title)->toBe('My Podcast')
        ->and($channel->language)->toBe('de');
});

test('Client can fetch episodes', function () {
    $client = createClientWithMock([
        new Response(200, [], json_encode([
            'data' => [
                [
                    'id' => 'ep-1',
                    'attributes' => [
                        'title' => 'Episode 1',
                        'description' => 'First',
                    ],
                ],
            ],
        ])),
    ]);

    $episodes = $client->episodes()->list('ch-1');

    expect($episodes)->toHaveCount(1)
        ->and($episodes[0])->toBeInstanceOf(Episode::class)
        ->and($episodes[0]->title)->toBe('Episode 1');
});

test('Client can fetch user', function () {
    $client = createClientWithMock([
        new Response(200, [], json_encode([
            'data' => [
                'id' => 42,
                'attributes' => [
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                ],
            ],
        ])),
    ]);

    $user = $client->users()->me();

    expect($user)->toBeInstanceOf(User::class)
        ->and($user->id)->toBe(42)
        ->and($user->name)->toBe('John Doe');
});

test('Client can fetch media', function () {
    $client = createClientWithMock([
        new Response(200, [], json_encode([
            'data' => [
                [
                    'id' => 'media-1',
                    'attributes' => [
                        'title' => 'audio.mp3',
                        'mimeType' => 'audio/mpeg',
                    ],
                ],
            ],
        ])),
    ]);

    $mediaList = $client->media()->list();

    expect($mediaList)->toHaveCount(1)
        ->and($mediaList[0])->toBeInstanceOf(Media::class)
        ->and($mediaList[0]->title)->toBe('audio.mp3');
});

test('Client throws NotAuthorizedException on 403', function () {
    $client = createClientWithMock([
        new Response(403, [], 'Forbidden'),
    ]);

    expect(fn () => $client->get('/api/channels'))
        ->toThrow(NotAuthorizedException::class);
});

test('Client throws UnexpectedResponseException on 500', function () {
    $client = createClientWithMock([
        new Response(500, [], 'Server Error'),
    ]);

    expect(fn () => $client->get('/api/channels'))
        ->toThrow(UnexpectedResponseException::class);
});

test('Client provides resource accessors', function () {
    $client = createClientWithMock([]);

    expect($client->channels())->toBeInstanceOf(\Podcaster\Resource\Channels::class)
        ->and($client->episodes())->toBeInstanceOf(\Podcaster\Resource\Episodes::class)
        ->and($client->media())->toBeInstanceOf(\Podcaster\Resource\MediaResource::class)
        ->and($client->users())->toBeInstanceOf(\Podcaster\Resource\Users::class);
});

test('extractAttributes handles JSON:API data.attributes format', function () {
    $result = PodcasterClient::extractAttributes([
        'data' => [
            'id' => 'ch-1',
            'attributes' => [
                'title' => 'Test',
            ],
        ],
    ]);

    expect($result['title'])->toBe('Test')
        ->and($result['uuid'])->toBe('ch-1');
});

test('extractAttributes handles flat attributes format', function () {
    $result = PodcasterClient::extractAttributes([
        'id' => 'ch-1',
        'attributes' => [
            'title' => 'Test',
        ],
    ]);

    expect($result['title'])->toBe('Test')
        ->and($result['uuid'])->toBe('ch-1');
});

test('extractAttributes returns raw array as fallback', function () {
    $result = PodcasterClient::extractAttributes([
        'title' => 'Test',
        'id' => 'ch-1',
    ]);

    expect($result['title'])->toBe('Test')
        ->and($result['id'])->toBe('ch-1');
});

test('withToken creates client with bearer token', function () {
    $mock = new MockHandler([
        new Response(200, [], json_encode([
            'data' => [
                'id' => 1,
                'attributes' => [
                    'name' => 'Fabio',
                    'email' => 'fabio@example.com',
                ],
            ],
        ])),
    ]);
    $httpClient = new Client(['handler' => HandlerStack::create($mock)]);

    $client = PodcasterClient::withToken('my-personal-access-token', $httpClient);

    expect($client->getToken()->token)->toBe('my-personal-access-token')
        ->and($client->getToken()->expire)->toBe(0);

    $user = $client->users()->me();
    expect($user->name)->toBe('Fabio');
});

test('withToken client does not require expiration date', function () {
    $client = PodcasterClient::withToken('some-token');

    expect($client)->toBeInstanceOf(PodcasterClient::class)
        ->and($client->getToken()->token)->toBe('some-token');
});
