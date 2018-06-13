<?php
/**
 * User: fabio
 * Date: 13.06.18
 * Time: 14:53
 */

namespace Podcaster\Client;


class Feed extends Client
{
    const APIURL_FEED = '/api/feeds/';

    public function get($id)
    {
        $url = $this->client->createApiUrl(self::APIURL_FEED . $id);
        $request = $this->client->createRequest('GET', $url);
        $result = $this->client->process($request);
        $oData = $this->client->decode($result, 'feed');

        return $oData;
    }
}