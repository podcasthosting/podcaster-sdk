<?php

declare(strict_types=1);

use Podcaster\Dto\User;

test('User can be created from array', function () {
    $user = User::fromArray([
        'id' => 42,
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);

    expect($user->id)->toBe(42)
        ->and($user->name)->toBe('John Doe')
        ->and($user->email)->toBe('john@example.com');
});

test('User casts id to int', function () {
    $user = User::fromArray([
        'id' => '42',
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);

    expect($user->id)->toBe(42);
});

test('User is json serializable', function () {
    $user = User::fromArray([
        'id' => 42,
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);

    $json = json_encode($user);
    $decoded = json_decode($json, true);

    expect($decoded['id'])->toBe(42)
        ->and($decoded['name'])->toBe('John Doe')
        ->and($decoded['email'])->toBe('john@example.com');
});
