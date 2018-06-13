<?php
/**
 * User: fabio
 * Date: 08.03.18
 * Time: 18:27
 */
namespace Podcaster;

use Buzz\Browser;
use Nyholm\Psr7\Request;
use Nyholm\Psr7\Uri;
use Podcaster\Token\Token;
use Psr\Http\Message\RequestInterface;

class PodcasterClient
{
    use PodcasterParseTrait;

    const API_SCHEME = 'https';
    const API_BASEURL = 'www.podcaster.de';
    const VERSION = '1.0.1';
    const USER_AGENT = 'PodcasterClient';

    private $browser;
    private $token;

    /**
     * PodcasterClient constructor.
     */
    public function __construct($accessToken, $accessTokenExpirationDate)
    {
        $this->setToken(new Token($accessToken, $accessTokenExpirationDate));
        $this->setBrowser(new Browser(new \Buzz\Client\Curl()));
    }
    /**
     * Setter injection.
     * Set HTTP client.
     *
     * @param Browser $browser
     */
    public function setBrowser(Browser $browser)
    {
        $this->browser = $browser;
    }

    public function getBrowser()
    {
        return $this->browser;
    }

    /**
     * Setter injection.
     * Set api token for authentification.
     *
     * @param Token $token
     */
    public function setToken(Token $token)
    {
        $this->token = $token;
    }

    public function createRequest($method, $url)
    {
        // create new request
        $request = new Request($method, $url);

        // add token if given
        if ($this->token) {
            /**
             * generate header for OAuth2 bearer token
             * http://self-issued.info/docs/draft-ietf-oauth-v2-bearer.html
             */
            $request = $request->withHeader('Authorization', sprintf("Bearer %s", $this->token->getToken()));
        }
        // set content type header
        $request = $request->withHeader("Content-Type", "application/json");
        // set user agent
        $request = $request->withHeader("User-Agent", self::USER_AGENT . '/' . self::VERSION);

        return $request;
    }

    public function process(RequestInterface $request)
    {
        $response = $this->browser->sendRequest($request);

        if ($response->getStatusCode() != 200) {
            throw new WrongStatusCodeException(sprintf("Invalid status code '%s'.", $response->getStatusCode()));
        }

        return $response->getBody();
    }

    /**
     * Creates a url object with the api base url and appends the $path
     *
     * @param string $path
     * @return Uri
     */
    public function createApiUrl($path): Uri
    {
        $uri = new Uri();
        $uri = $uri->withScheme(self::API_SCHEME);
        $uri = $uri->withHost(self::API_BASEURL);
        $uri = $uri->withPath($path);

        return $uri;
    }

    public function decode($content, $type = null)
    {
        $result = json_decode($content);
        $type = strtolower($type);

        switch($type) {
            case "feed":
                $result = $result->attributes;
        }

        return $this->convert($result, $type);
    }

    public function convert($result, $type = null)
    {
        $type = strtolower($type);

        switch($type) {
            case "feed":
                $oFeed = new Resource\Feed();
                self::cast($oFeed, $result);

                return $oFeed;
            default:
                return $result;
        }
    }
}