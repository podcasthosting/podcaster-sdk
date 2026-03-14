<?php

declare(strict_types=1);

use Podcaster\Dto\Channel;
use Podcaster\Dto\Channel\Itunes;
use Podcaster\Dto\Channel\PodcastIndex;

test('Channel can be created from array', function () {
    $channel = Channel::fromArray([
        'uuid' => 'abc-123',
        'title' => 'My Podcast',
        'link' => 'https://example.com',
        'description' => 'A great podcast',
        'author' => 'John Doe',
        'email' => 'john@example.com',
        'copyright' => '2024',
        'language' => 'de',
        'category' => 'Technology',
    ]);

    expect($channel->uuid)->toBe('abc-123')
        ->and($channel->title)->toBe('My Podcast')
        ->and($channel->link)->toBe('https://example.com')
        ->and($channel->description)->toBe('A great podcast')
        ->and($channel->author)->toBe('John Doe')
        ->and($channel->email)->toBe('john@example.com')
        ->and($channel->language)->toBe('de')
        ->and($channel->category)->toBe('Technology');
});

test('Channel can be created with nested itunes data', function () {
    $channel = Channel::fromArray([
        'uuid' => 'abc-123',
        'title' => 'My Podcast',
        'itunes' => [
            'subtitle' => 'A subtitle',
            'explicit' => 'no',
            'type' => 'episodic',
            'category' => ['Technology', 'Science'],
        ],
    ]);

    expect($channel->itunes)->toBeInstanceOf(Itunes::class)
        ->and($channel->itunes->subtitle)->toBe('A subtitle')
        ->and($channel->itunes->explicit)->toBe('no')
        ->and($channel->itunes->type)->toBe('episodic')
        ->and($channel->itunes->category)->toBe(['Technology', 'Science']);
});

test('Channel can be created with podcastIndex data', function () {
    $channel = Channel::fromArray([
        'uuid' => 'abc-123',
        'title' => 'My Podcast',
        'podcastIndex' => [
            'locked' => 'yes',
            'funding' => 'Support us',
            'fundingUrl' => 'https://example.com/donate',
        ],
    ]);

    expect($channel->podcastIndex)->toBeInstanceOf(PodcastIndex::class)
        ->and($channel->podcastIndex->locked)->toBe('yes')
        ->and($channel->podcastIndex->fundingUrl)->toBe('https://example.com/donate');
});

test('Channel uses id field as uuid fallback', function () {
    $channel = Channel::fromArray([
        'id' => 'fallback-id',
        'title' => 'Test',
    ]);

    expect($channel->uuid)->toBe('fallback-id');
});

test('Channel is json serializable', function () {
    $channel = Channel::fromArray([
        'uuid' => 'abc-123',
        'title' => 'My Podcast',
    ]);

    $json = json_encode($channel);
    $decoded = json_decode($json, true);

    expect($decoded['uuid'])->toBe('abc-123')
        ->and($decoded['title'])->toBe('My Podcast');
});
