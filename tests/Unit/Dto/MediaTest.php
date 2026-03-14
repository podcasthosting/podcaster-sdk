<?php

declare(strict_types=1);

use Podcaster\Dto\Media;

test('Media can be created from array', function () {
    $media = Media::fromArray([
        'id' => 'media-123',
        'title' => 'audio.mp3',
        'mimeType' => 'audio/mpeg',
        'size' => '1024000',
        'duration' => '300',
    ]);

    expect($media->id)->toBe('media-123')
        ->and($media->title)->toBe('audio.mp3')
        ->and($media->mimeType)->toBe('audio/mpeg')
        ->and($media->size)->toBe('1024000')
        ->and($media->duration)->toBe('300');
});

test('Media handles null optional fields', function () {
    $media = Media::fromArray([
        'id' => 'media-123',
        'title' => 'audio.mp3',
    ]);

    expect($media->mimeType)->toBeNull()
        ->and($media->size)->toBeNull()
        ->and($media->duration)->toBeNull();
});

test('Media is json serializable', function () {
    $media = Media::fromArray([
        'id' => 'media-123',
        'title' => 'audio.mp3',
        'mimeType' => 'audio/mpeg',
    ]);

    $json = json_encode($media);
    $decoded = json_decode($json, true);

    expect($decoded['id'])->toBe('media-123')
        ->and($decoded['title'])->toBe('audio.mp3')
        ->and($decoded['mimeType'])->toBe('audio/mpeg');
});
