# Podcaster SDK

PHP SDK for the [podcaster.de](https://www.podcaster.de) API. Manage channels, episodes, media and users programmatically.

**Requirements:** PHP >= 8.3

## Installation

```bash
composer require podcasthosting/podcaster-sdk
```

## Authentication

The SDK supports two authentication methods.

### Bearer Token (Personal Access Token)

The simplest option — use a Personal Access Token directly:

```php
use Podcaster\PodcasterClient;

$client = PodcasterClient::withToken('your-personal-access-token');
```

### OAuth 2.0

For applications that authenticate on behalf of a user:

```php
use Podcaster\PodcasterAuthClient;
use Podcaster\PodcasterClient;

// 1. Set up the OAuth client
$auth = new PodcasterAuthClient(
    clientId: 'your-client-id',
    clientSecret: 'your-client-secret',
    redirectUri: 'https://your-app.com/callback',
);

// 2. Run the authorization flow (redirects the user)
$auth->authorize();

// 3. After callback, get the access token
$accessToken = $auth->getAccessToken();

// 4. Create the API client
$client = new PodcasterClient(
    accessToken: $accessToken->getToken(),
    accessTokenExpirationDate: $accessToken->getExpires(),
);
```

## Usage

All API resources are available via the fluent interface on `PodcasterClient`.

### Channels

```php
// List all channels
$channels = $client->channels()->list();

// With pagination
$channels = $client->channels()->list(page: 1, size: 10);

// Get a single channel
$channel = $client->channels()->get('channel-uuid');

echo $channel->title;
echo $channel->description;
echo $channel->language;

// Create a channel
$channel = $client->channels()->create([
    'title' => 'My New Podcast',
    'description' => 'A podcast about...',
    'language' => 'de',
]);

// Delete a channel
$client->channels()->delete('channel-uuid');
```

### Episodes

```php
// List episodes for a channel
$episodes = $client->episodes()->list('channel-uuid');

// With pagination
$episodes = $client->episodes()->list('channel-uuid', page: 1, size: 20);

// Get a single episode
$episode = $client->episodes()->get('episode-uuid');

echo $episode->title;
echo $episode->publishingDate;

// Create an episode
$episode = $client->episodes()->create([
    'title' => 'Episode 1',
    'description' => 'First episode',
    'channel' => 'channel-uuid',
]);

// Copy an episode (optionally to another channel)
$copy = $client->episodes()->copy('episode-uuid', targetChannelUuid: 'other-channel-uuid');

// Delete an episode
$client->episodes()->delete('episode-uuid');
```

### Media

```php
// List all media files
$mediaList = $client->media()->list();

// Sorted
$mediaList = $client->media()->list(sortBy: 'title', sortDir: 'asc');

// Get a single media item
$media = $client->media()->get('media-id');

echo $media->title;
echo $media->mimeType;
echo $media->duration;

// Upload a file
$media = $client->media()->upload('/path/to/audio.mp3');

// Get / update metadata
$metadata = $client->media()->getMetadata('media-id');
$metadata = $client->media()->updateMetadata('media-id', [
    'title' => 'Updated Title',
]);

// Delete a media item
$client->media()->delete('media-id');
```

### Users

```php
// Get the authenticated user
$user = $client->users()->me();

echo $user->name;
echo $user->email;

// Update a user
$user = $client->users()->update($user->id, [
    'name' => 'New Name',
]);
```

## Error Handling

The SDK throws typed exceptions for different error scenarios:

```php
use Podcaster\Exceptions\NotAuthorizedException;
use Podcaster\Exceptions\ApiException;
use Podcaster\Exceptions\UnexpectedResponseException;

try {
    $channel = $client->channels()->get('channel-uuid');
} catch (NotAuthorizedException $e) {
    // 403 — invalid or expired token
} catch (UnexpectedResponseException $e) {
    // Other non-2xx status codes
    echo $e->getStatusCode();
    echo $e->getResponseBody();
} catch (ApiException $e) {
    // Network or connection errors
}
```

## Development

```bash
# Install dependencies
composer install

# Run tests
./vendor/bin/pest

# Static analysis (level 6)
./vendor/bin/phpstan analyse

# Code style (PSR-12)
./vendor/bin/php-cs-fixer fix
```

## License

GPL-2.0+
