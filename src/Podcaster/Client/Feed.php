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

    /**
     * @param $id
     * @param bool $convert
     * @return \Podcaster\Resource\Feed
     * @throws \Podcaster\WrongStatusCodeException
     * @throws \Exception
     */
    public function get($id, $convert = true)
    {
        $url = $this->client->createApiUrl(self::APIURL_FEED . $id);
        $request = $this->client->createRequest('GET', $url);

        if (is_subclass_of($request, '\Psr\Http\Message\RequestInterface')) {
            $result = $this->client->process($request);
        }

        if ($convert === true) {
            return $this->client->decode($result, 'feed');
        }

        return $result;
    }
}