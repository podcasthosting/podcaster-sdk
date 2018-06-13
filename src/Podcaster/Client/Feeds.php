<?php
/**
 * User: fabio
 * Date: 13.06.18
 * Time: 14:53
 */

namespace Podcaster\Client;


class Feeds extends Client
{
    const APIURL_FEEDS = '/api/feeds';

    public function getList()
    {
        $url = $this->client->createApiUrl(self::APIURL_FEEDS);
        $request = $this->client->createRequest('GET', $url);
        $result = $this->client->process($request);
        $oData = $this->client->decode($result);

        $feeds = [];

        if($oData->data && is_array($oData->data)) {
            foreach($oData->data as $feed) {
                $feeds[] = $this->load($feed->id);
            }
        }

        return $feeds;
    }

    private function load($id)
    {
        return (new Feed($this->getClient()))->get($id);
    }
}