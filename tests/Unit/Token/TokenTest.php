<?php

declare(strict_types=1);

use Podcaster\Exceptions\TokenException;
use Podcaster\Token\Token;

test('Token can be constructed with all parameters', function () {
    $token = new Token('abc123', 9999999999, 'bearer', 'read');

    expect($token->token)->toBe('abc123')
        ->and($token->expire)->toBe(9999999999)
        ->and($token->type)->toBe('bearer')
        ->and($token->scope)->toBe('read');
});

test('Token has default values for type and scope', function () {
    $token = new Token('abc123', 9999999999);

    expect($token->type)->toBe('bearer')
        ->and($token->scope)->toBe('');
});

test('Token isExpired returns false for future expiry', function () {
    $token = new Token('abc123', time() + 3600);

    expect($token->isExpired())->toBeFalse();
});

test('Token isExpired returns true for past expiry', function () {
    $token = new Token('abc123', time() - 3600);

    expect($token->isExpired())->toBeTrue();
});

test('Token isExpired throws on zero expire', function () {
    $token = new Token('abc123', 0);

    expect(fn () => $token->isExpired())->toThrow(TokenException::class);
});

test('Token can be serialized and unserialized', function () {
    $token = new Token('abc123', 9999999999, 'bearer', 'read');

    $serialized = serialize($token);
    $unserialized = unserialize($serialized);

    expect($unserialized->token)->toBe('abc123')
        ->and($unserialized->expire)->toBe(9999999999)
        ->and($unserialized->type)->toBe('bearer')
        ->and($unserialized->scope)->toBe('read');
});
