<?php
/**
 * User: fabio
 * Date: 08.03.18
 * Time: 18:35
 */

namespace Podcaster;

use Podcaster\Exceptions\InvalidStateException;

class PodcasterAuthClient
{
    const PODCASTER_API_URI = 'https://www.podcaster.de';
    const ACCESSTOKEN_URI = '/oauth/token';
    const AUTHORIZE_URI = '/oauth/authorize';
    const RESOURCE_URI = '/oauth/personal-access-tokens';

    private $provider;

    private $accessToken;

    /**
     * PodcasterAuthClient constructor.
     */
    /**
     * PodcasterAuthClient constructor.
     * @param $clientId string The client ID assigned to you by the provider
     * @param $clientSecret string The client password assigned to you by the provider
     * @param $redirectUri
     * @param $apiUrl
     */
    public function __construct($clientId, $clientSecret, $redirectUri, $apiUrl = self::PODCASTER_API_URI)
    {
        $apiUrl = rtrim($apiUrl, '/');
        $this->provider = new \League\OAuth2\Client\Provider\GenericProvider([
            'clientId'                => $clientId,
            'clientSecret'            => $clientSecret,
            'scopes'                  => ['*'],
            'redirectUri'             => $redirectUri,
            'urlAuthorize'            => $apiUrl . self::AUTHORIZE_URI,
            'urlAccessToken'          => $apiUrl . self::ACCESSTOKEN_URI,
            'urlResourceOwnerDetails' => $apiUrl . self::RESOURCE_URI,
        ]);
    }

    public function authorize()
    {
        $provider = $this->provider;

        // If we don't have an authorization code then get one
        if (!isset($_GET['code'])) {

            // Fetch the authorization URL from the provider; this returns the
            // urlAuthorize option and generates and applies any necessary parameters
            // (e.g. state).
            $authorizationUrl = $provider->getAuthorizationUrl();

            // Get the state generated for you and store it to the session.
            $_SESSION['oauth2state'] = $provider->getState();

            // Redirect the user to the authorization URL.
            header('Location: ' . $authorizationUrl);
            exit;

        // Check given state against previously stored one to mitigate CSRF attack
        } elseif (empty($_GET['state']) || (isset($_SESSION['oauth2state']) && $_GET['state'] !== $_SESSION['oauth2state'])) {

            if (isset($_SESSION['oauth2state'])) {
                unset($_SESSION['oauth2state']);
            }

            // Should never occur in a working setup with valid requests
            throw new InvalidStateException('Invalid state');
        } else {
                // Try to get an access token using the authorization code grant.
                $accessToken = $provider->getAccessToken('authorization_code', ['code' => $_GET['code']]);

                // We have an access token, which we may use in authenticated
                // requests against the service provider's API.
                $this->setAccessToken($accessToken);
        }
    }

    /**
     * @param $existingAccessToken
     * @throws InvalidStateException
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function refresh($existingAccessToken)
    {
        if ($existingAccessToken && $existingAccessToken->hasExpired()) {
            $newAccessToken = $this->provider->getAccessToken('refresh_token', [
                'refresh_token' => $existingAccessToken->getRefreshToken()
            ]);

            $this->setAccessToken($newAccessToken);
        }
    }

    /**
     * @return mixed
     */
    public function getAccessToken()
    {
        $this->refresh($this->accessToken);

        return $this->accessToken;
    }

    /**
     * @param mixed $accessToken
     */
    public function setAccessToken($accessToken): void
    {
        $this->accessToken = $accessToken;
    }
}
