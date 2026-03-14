<?php

declare(strict_types=1);

namespace Podcaster\Resource;

use Podcaster\PodcasterClient;

abstract class AbstractResource
{
    public function __construct(
        protected readonly PodcasterClient $client,
    ) {
    }
}
