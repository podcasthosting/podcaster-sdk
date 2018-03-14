<?php
/**
 * User: fabio
 * Date: 08.03.18
 * Time: 18:27
 */
namespace Podcaster;

class Podcaster
{
    use PodcasterParseTrait;

    const API_BASEURL = "https://api.podcaster.de/";

    const VERSION = '1.0.0';

    protected $client;

    protected $oAuth2Client;

    /**
     * Podcaster constructor.
     * @param $oAuth2Client
     */
    public function __construct($clientId, $clientSecret, $redirectUri)
    {
        $this->client = new PodcasterClient($clientId, $clientSecret, $redirectUri, self::API_BASEURL);
    }

    /**
     * @return PodcasterClient
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