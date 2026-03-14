<?php

declare(strict_types=1);

namespace Podcaster\Token;

use Podcaster\Exceptions\TokenException;

readonly class Token
{
    public const string ID_TOKEN = 'token';
    public const string ID_EXPIRE = 'expire';
    public const string ID_TYPE = 'type';
    public const string ID_SCOPE = 'scope';

    public function __construct(
        public string $token,
        public int $expire,
        public string $type = 'bearer',
        public string $scope = '',
    ) {
    }

    public function isExpired(): bool
    {
        if ($this->expire === 0) {
            throw new TokenException('No expire date set for this token.');
        }

        return $this->expire <= time();
    }

    /** @return array{token: string, expire: int, type: string, scope: string} */
    public function __serialize(): array
    {
        return [
            self::ID_TOKEN => $this->token,
            self::ID_EXPIRE => $this->expire,
            self::ID_TYPE => $this->type,
            self::ID_SCOPE => $this->scope,
        ];
    }

    /** @param array{token: string, expire: int, type: string, scope: string} $data */
    public function __unserialize(array $data): void
    {
        // readonly properties cannot be set in __unserialize after construction,
        // so we use reflection as a workaround for deserialization
        $ref = new \ReflectionClass($this);
        foreach ([self::ID_TOKEN, self::ID_EXPIRE, self::ID_TYPE, self::ID_SCOPE] as $key) {
            if (array_key_exists($key, $data)) {
                $ref->getProperty($key)->setValue($this, $data[$key]);
            }
        }
    }
}
