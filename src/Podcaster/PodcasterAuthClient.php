<?php

declare(strict_types=1);

namespace Podcaster;

use League\OAuth2\Client\Provider\GenericProvider;
use League\OAuth2\Client\Token\AccessTokenInterface;
use Podcaster\Exceptions\InvalidStateException;

class PodcasterAuthClient
{
    public const string PODCASTER_API_URI = 'https://app.podcaster.de';
    public const string ACCESSTOKEN_URI = '/oauth/token';
    public const string AUTHORIZE_URI = '/oauth/authorize';
    public const string RESOURCE_URI = '/oauth/personal-access-tokens';

    private GenericProvider $provider;
    private ?AccessTokenInterface $accessToken = null;

    public function __construct(
        string $clientId,
        string $clientSecret,
        string $redirectUri,
        string $apiUrl = self::PODCASTER_API_URI,
    ) {
        $apiUrl = rtrim($apiUrl, '/');
        $this->provider = new GenericProvider([
            'clientId' => $clientId,
            'clientSecret' => $clientSecret,
            'scopes' => ['*'],
            'redirectUri' => $redirectUri,
            'urlAuthorize' => $apiUrl . self::AUTHORIZE_URI,
            'urlAccessToken' => $apiUrl . self::ACCESSTOKEN_URI,
            'urlResourceOwnerDetails' => $apiUrl . self::RESOURCE_URI,
        ]);
    }

    public function authorize(): void
    {
        $provider = $this->provider;

        if (!isset($_GET['code'])) {
            $authorizationUrl = $provider->getAuthorizationUrl();
            $_SESSION['oauth2state'] = $provider->getState();
            header('Location: ' . $authorizationUrl);
            exit;
        } elseif (empty($_GET['state']) || (isset($_SESSION['oauth2state']) && $_GET['state'] !== $_SESSION['oauth2state'])) {
            if (isset($_SESSION['oauth2state'])) {
                unset($_SESSION['oauth2state']);
            }
            throw new InvalidStateException('Invalid state');
        } else {
            $accessToken = $provider->getAccessToken('authorization_code', ['code' => $_GET['code']]);
            $this->setAccessToken($accessToken);
        }
    }

    public function refresh(AccessTokenInterface $existingAccessToken): void
    {
        if ($existingAccessToken->hasExpired()) {
            $newAccessToken = $this->provider->getAccessToken('refresh_token', [
                'refresh_token' => $existingAccessToken->getRefreshToken(),
            ]);
            $this->setAccessToken($newAccessToken);
        }
    }

    public function getAccessToken(): ?AccessTokenInterface
    {
        if ($this->accessToken !== null) {
            $this->refresh($this->accessToken);
        }

        return $this->accessToken;
    }

    public function setAccessToken(AccessTokenInterface $accessToken): void
    {
        $this->accessToken = $accessToken;
    }
}
