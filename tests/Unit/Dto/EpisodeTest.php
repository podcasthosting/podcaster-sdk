<?php

declare(strict_types=1);

use Podcaster\Dto\Episode;
use Podcaster\Dto\Episode\Itunes;

test('Episode can be created from array', function () {
    $episode = Episode::fromArray([
        'uuid' => 'ep-123',
        'title' => 'Episode 1',
        'description' => 'First episode',
        'author' => 'John Doe',
        'publishingDate' => '2024-01-01',
        'publishingTime' => '12:00',
        'mediaId' => 'media-456',
    ]);

    expect($episode->uuid)->toBe('ep-123')
        ->and($episode->title)->toBe('Episode 1')
        ->and($episode->description)->toBe('First episode')
        ->and($episode->publishingDate)->toBe('2024-01-01')
        ->and($episode->mediaId)->toBe('media-456');
});

test('Episode can be created with itunes data', function () {
    $episode = Episode::fromArray([
        'uuid' => 'ep-123',
        'title' => 'Episode 1',
        'itunes' => [
            'subtitle' => 'Ep subtitle',
            'explicit' => 'no',
            'episodeType' => 'full',
            'season' => 1,
            'episode' => 5,
        ],
    ]);

    expect($episode->itunes)->toBeInstanceOf(Itunes::class)
        ->and($episode->itunes->episodeType)->toBe('full')
        ->and($episode->itunes->season)->toBe(1)
        ->and($episode->itunes->episode)->toBe(5);
});

test('Episode handles null optional fields', function () {
    $episode = Episode::fromArray([
        'uuid' => 'ep-123',
        'title' => 'Episode 1',
    ]);

    expect($episode->publishingDate)->toBeNull()
        ->and($episode->mediaId)->toBeNull()
        ->and($episode->itunes)->toBeNull();
});

test('Episode is json serializable', function () {
    $episode = Episode::fromArray([
        'uuid' => 'ep-123',
        'title' => 'Episode 1',
    ]);

    $json = json_encode($episode);
    $decoded = json_decode($json, true);

    expect($decoded['uuid'])->toBe('ep-123')
        ->and($decoded['title'])->toBe('Episode 1');
});
