<?php
/**
 * User: fabio
 * Date: 13.06.18
 * Time: 15:11
 */

namespace Podcaster\Client;

use Podcaster\PodcasterClient;

abstract class Client
{
    protected $client;

    /**
     * Client constructor.
     * @param $client
     */
    public function __construct(PodcasterClient $client)
    {
        $this->client = $client;
    }

    /**
     * @return mixed
     */
    public function getClient(): PodcasterClient
    {
        return $this->client;
    }

    /**
     * @param PodcasterClient $client
     */
    public function setClient(PodcasterClient $client): void
    {
        $this->client = $client;
    }
}
