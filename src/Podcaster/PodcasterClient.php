<?php

declare(strict_types=1);

namespace Podcaster;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Utils;
use Podcaster\Exceptions\ApiException;
use Podcaster\Exceptions\NotAuthorizedException;
use Podcaster\Exceptions\UnexpectedResponseException;
use Podcaster\Resource\Channels;
use Podcaster\Resource\Episodes;
use Podcaster\Resource\MediaResource;
use Podcaster\Resource\Users;
use Podcaster\Token\Token;

class PodcasterClient
{
    public const string API_SCHEME = 'https';
    public const string API_BASEURL = 'app.podcaster.de';
    public const string VERSION = '2.0.0';
    public const string USER_AGENT = 'PodcasterClient';

    private Client $httpClient;
    private Token $token;

    public function __construct(
        string $accessToken,
        int $accessTokenExpirationDate = 0,
        ?Client $httpClient = null,
    ) {
        $this->token = new Token($accessToken, $accessTokenExpirationDate);
        $this->httpClient = $httpClient ?? new Client([
            'base_uri' => self::API_SCHEME . '://' . self::API_BASEURL,
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);
    }

    /**
     * Create a client using a plain Bearer token (e.g. a Personal Access Token).
     */
    public static function withToken(string $bearerToken, ?Client $httpClient = null): self
    {
        return new self(
            accessToken: $bearerToken,
            httpClient: $httpClient,
        );
    }

    public function getToken(): Token
    {
        return $this->token;
    }

    public function getHttpClient(): Client
    {
        return $this->httpClient;
    }

    public function channels(): Channels
    {
        return new Channels($this);
    }

    public function episodes(): Episodes
    {
        return new Episodes($this);
    }

    public function media(): MediaResource
    {
        return new MediaResource($this);
    }

    public function users(): Users
    {
        return new Users($this);
    }

    /**
     * @deprecated Use channels() instead
     */
    public function feeds(): \Podcaster\Client\Feeds
    {
        return new \Podcaster\Client\Feeds($this);
    }

    /**
     * @return array<string, string>
     */
    private function defaultHeaders(): array
    {
        return [
            'Authorization' => sprintf('Bearer %s', $this->token->token),
            'Content-Type' => 'application/json',
            'User-Agent' => self::USER_AGENT . '/' . self::VERSION,
            'X-Requested-With' => 'XMLHttpRequest',
        ];
    }

    /**
     * @param array<string, mixed> $options
     */
    public function get(string $path, array $options = []): mixed
    {
        return $this->request('GET', $path, $options);
    }

    /**
     * @param array<string, mixed> $options
     */
    public function post(string $path, array $options = []): mixed
    {
        return $this->request('POST', $path, $options);
    }

    /**
     * @param array<string, mixed> $options
     */
    public function put(string $path, array $options = []): mixed
    {
        return $this->request('PUT', $path, $options);
    }

    /**
     * @param array<string, mixed> $options
     */
    public function delete(string $path, array $options = []): mixed
    {
        return $this->request('DELETE', $path, $options);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function postJson(string $path, array $data): mixed
    {
        return $this->request('POST', $path, ['json' => $data]);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function putJson(string $path, array $data): mixed
    {
        return $this->request('PUT', $path, ['json' => $data]);
    }

    public function uploadFile(string $path, string $filePath): mixed
    {
        return $this->request('POST', $path, [
            'multipart' => [
                [
                    'name' => 'file',
                    'contents' => Utils::tryFopen($filePath, 'r'),
                    'filename' => basename($filePath),
                ],
            ],
        ]);
    }

    /**
     * @param array<string, mixed> $options
     */
    private function request(string $method, string $path, array $options = []): mixed
    {
        $headers = $this->defaultHeaders();

        // Don't set Content-Type for multipart (Guzzle sets it with boundary)
        if (isset($options['multipart'])) {
            unset($headers['Content-Type']);
        }

        $options['headers'] = array_merge($headers, $options['headers'] ?? []);
        $options['http_errors'] = false;

        try {
            $response = $this->httpClient->request($method, $path, $options);
        } catch (GuzzleException $e) {
            throw new ApiException(
                message: $e->getMessage(),
                statusCode: (int) $e->getCode(),
                previous: $e,
            );
        }

        $statusCode = $response->getStatusCode();
        $body = (string) $response->getBody();

        return match (true) {
            $statusCode >= 200 && $statusCode < 300 => $this->decodeResponse($body),
            $statusCode === 403 => throw new NotAuthorizedException(
                message: 'Authorisation failed. Check your credentials.',
                statusCode: $statusCode,
                responseBody: $body,
            ),
            default => throw new UnexpectedResponseException(
                message: sprintf("Unexpected status code: '%d - %s'", $statusCode, $response->getReasonPhrase()),
                statusCode: $statusCode,
                responseBody: $body,
            ),
        };
    }

    private function decodeResponse(string $body): mixed
    {
        if ($body === '') {
            return null;
        }

        $decoded = json_decode($body, true, 512, JSON_THROW_ON_ERROR);

        return $decoded;
    }

    /**
     * Extract attributes from a JSON:API style response.
     *
     * @param array<string, mixed> $response
     * @return array<string, mixed>
     */
    public static function extractAttributes(array $response): array
    {
        if (isset($response['data']['attributes'])) {
            $attributes = $response['data']['attributes'];
            if (isset($response['data']['id'])) {
                $attributes['uuid'] = $response['data']['id'];
            }
            return $attributes;
        }

        if (isset($response['attributes'])) {
            $attributes = $response['attributes'];
            if (isset($response['id'])) {
                $attributes['uuid'] = $response['id'];
            }
            return $attributes;
        }

        return $response;
    }

    /**
     * Extract a list of items from a JSON:API style response.
     *
     * @param array<string, mixed> $response
     * @return list<array<string, mixed>>
     */
    public static function extractDataList(array $response): array
    {
        if (isset($response['data']) && is_array($response['data'])) {
            return array_values($response['data']);
        }

        return [];
    }

    // Legacy compatibility methods for Client\Feed and Client\Feeds

    /**
     * @deprecated Use get() instead
     */
    public function createApiUrl(string $path): string
    {
        return $path;
    }

    /**
     * @deprecated Use get() instead
     */
    public function createRequest(string $method, string $url): \GuzzleHttp\Psr7\Request
    {
        return new \GuzzleHttp\Psr7\Request($method, $url, $this->defaultHeaders());
    }

    /**
     * @deprecated Use get() instead
     */
    public function process(\Psr\Http\Message\RequestInterface $request): string
    {
        try {
            $response = $this->httpClient->send($request);
        } catch (GuzzleException $e) {
            throw new ApiException(
                message: $e->getMessage(),
                statusCode: (int) $e->getCode(),
                previous: $e,
            );
        }

        $statusCode = $response->getStatusCode();
        $body = (string) $response->getBody();

        return match (true) {
            $statusCode >= 200 && $statusCode < 300 => $body,
            $statusCode === 403 => throw new NotAuthorizedException(
                message: 'Authorisation failed. Check your credentials.',
                statusCode: $statusCode,
                responseBody: $body,
            ),
            default => throw new UnexpectedResponseException(
                message: sprintf("Unexpected status code: '%d - %s'", $statusCode, $response->getReasonPhrase()),
                statusCode: $statusCode,
                responseBody: $body,
            ),
        };
    }

    /**
     * @deprecated Legacy decode method
     */
    public function decode(string $content, ?string $type = null): mixed
    {
        $result = json_decode($content);

        if ($type !== null && strtolower($type) === 'feed') {
            $result = $result->attributes ?? $result;
        }

        return $this->convert($result, $type);
    }

    /**
     * @deprecated Legacy convert method
     */
    public function convert(mixed $result, ?string $type = null): mixed
    {
        if ($type !== null && strtolower($type) === 'feed') {
            $oFeed = new Resource\Feed();
            Resource\Feed::cast($oFeed, $result);
            return $oFeed;
        }

        return $result;
    }
}
