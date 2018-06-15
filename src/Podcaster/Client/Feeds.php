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

    public function getList($convert = true)
    {
        $url = $this->client->createApiUrl(self::APIURL_FEEDS);
        $request = $this->client->createRequest('GET', $url);
        $result = $this->client->process($request);

        if ($convert === true) {
            $oData = $this->client->decode($result);

            $feeds = [];

            if($oData->data && is_array($oData->data)) {
                foreach($oData->data as $feed) {
                    $feeds[$feed->id] = $this->load($feed->id, $convert);
                }
            }

            return $feeds;
        }

        return (string)$result;
    }

    private function load($id, $convert)
    {
        return (new Feed($this->getClient()))->get($id, $convert);
    }
}