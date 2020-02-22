<?php
/**
 * User: fabio
 * Date: 08.03.18
 * Time: 18:27
 */
namespace Podcaster;

use Buzz\Browser;
use Buzz\Client\Curl;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use Podcaster\Token\Token;
use Psr\Http\Message\RequestInterface;
use Tuupola\Http\Factory\RequestFactory;
use Tuupola\Http\Factory\ResponseFactory;

class PodcasterClient
{
    use PodcasterParseTrait;

    const API_SCHEME = 'https';
    const API_BASEURL = 'www.podcaster.de';
    const VERSION = '1.1.2';
    const USER_AGENT = 'PodcasterClient';

    private $browser;
    private $token;

    /**
     * PodcasterClient constructor.
     */
    public function __construct($accessToken, $accessTokenExpirationDate)
    {
        $this->setToken(new Token($accessToken, $accessTokenExpirationDate));
        $this->setBrowser(new Browser(new Curl(new ResponseFactory()), new RequestFactory()));
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

    /**
     * @param $method
     * @param $url
     * @return Request
     * @throws \Exception
     */
    public function createRequest(string $method, string $url)
    {
        if (!$this->token) {
            throw new \Exception("Token is required");
        }
        // create new request
        $headers = [
            'Authorization' => sprintf("Bearer %s", $this->token->getToken()),
            // set content type header
            "Content-Type" => "application/json",
            // set user agent
            "User-Agent" => self::USER_AGENT . '/' . self::VERSION,
            // Send as AJAX request for more useful error codes
            "X-Requested-With" => "XMLHttpRequest",
        ];
        $request = new Request($method, $url, $headers);

        return $request;
    }

    /**
     * @param RequestInterface $request
     * @return mixed
     * @throws WrongStatusCodeException
     */
    public function process(RequestInterface $request)
    {
        $response = $this->browser->sendRequest($request);

        if ($response->getStatusCode() != 200) {
            throw new WrongStatusCodeException(sprintf("Invalid status code: '%s'", $response->getStatusCode() . ' - ' .  $response->getReasonPhrase()));
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
