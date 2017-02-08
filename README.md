# EveOnline Provider for OAuth 2.0 Client

This package provides EveOnline OAuth 2.0 support for the PHP League's [OAuth 2.0 Client](https://github.com/thephpleague/oauth2-client).


## Installation

To install, use composer:

```
composer require alcohol/oauth2-eveonline
```


## Usage

Have a look at the [examples](example/).

### Basic Authentication Flow

```php
$provider = new Alcohol\OAuth2\Client\Provider\EveOnline([
    'clientId' => '{client-id}',
    'clientSecret' => '{client-secret}',
    'redirectUri' => 'https://example.com/callback-url',
    // the following are optional and displayed here with their default values
    'urlAuthorize' => 'https://login.eveonline.com/oauth/authorize',
    'urlAccessToken' => 'https://login.eveonline.com/oauth/token',
    'urlResourceOwnerDetails' => 'https://login.eveonline.com/oauth/verify',
]);

// If we don't have an authorization code then get one
if (!isset($_GET['code'])) {

    // Fetch the authorization URL from the provider; this returns the
    // urlAuthorize option and generates and applies any necessary parameters
    // (e.g. state).
    $authorizationUrl = $provider->getAuthorizationUrl();

    // Get the state generated for you and store it in the session.
    $_SESSION['oauth2state'] = $provider->getState();

    // Redirect the user to the authorization URL.
    header('Location: ' . $authorizationUrl);
    exit;

// Check given state against previously stored one to mitigate CSRF attacks.
} elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {

    unset($_SESSION['oauth2state']);
    exit('Invalid state');

} else {

    try {

        // Try to get an access token using the authorization code grant.
        $accessToken = $provider->getAccessToken('authorization_code', [
            'code' => $_GET['code']
        ]);

        // We have an access token, which we may use in authenticated
        // requests against the service provider's API.
        echo $accessToken->getToken() . "\n";
        echo $accessToken->getRefreshToken() . "\n";
        echo $accessToken->getExpires() . "\n";
        echo ($accessToken->hasExpired() ? 'expired' : 'not expired') . "\n";

        // Using the access token, we may look up details about the
        // resource owner.
        $resourceOwner = $provider->getResourceOwner($accessToken);

        var_export($resourceOwner->toArray());

    } catch (League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {

        // Failed to get the access token or user details.
        exit($e->getMessage());
    }
}
```

### Authenticated Requests

```php
// The provider provides a way to get an authenticated API request for
// the service, using the access token; it returns an object conforming
// to Psr\Http\Message\RequestInterface.
$request = $provider->getAuthenticatedRequest(
    'GET',
    sprintf('https://crest-tq.eveonline.com/characters/%u/', $user->getId()),
    $accessToken
);

$client = new GuzzleHttp\Client();
$response = $client->send($request);

print $response->getBody();
```

### Refresh Token

```php
// Make sure you check if your token is expired or not, and refresh it when necessary
$existingAccessToken = someMethodThatRetrievesStoredToken();

if ($existingAccessToken->hasExpired()) {
    $newAccessToken = $provider->getAccessToken('refresh_token', [
        'refresh_token' => $existingAccessToken->getRefreshToken()
    ]);

    // Purge old access token and store new access token
}
```


## Testing

> Sorry, I got lazy. Just don't break the examples.


## Contributing

Feel free to submit a pull request or create an issue.


## License

alcohol/oauth2-ccp is licensed under the MIT license.
