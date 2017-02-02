<?php

/*
 * (c) Rob Bast <rob.bast@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

require '../vendor/autoload.php';

session_start();

$provider = new Alcohol\OAuth2\Client\Provider\EveOnline([
    'clientId' => '{client-id}',
    'clientSecret' => '{client-secret}',
    'redirectUri' => 'https://example.com/callback-url',
]);

// If we don't have an authorization code then get one
if (!isset($_GET['code'])) {
    $authUrl = $provider->getAuthorizationUrl([
        'scope' => [
            'characterAccountRead',
            'characterAssetsRead',
            'characterBookmarksRead',
            // etc
        ],
    ]);
    $_SESSION['oauth2state'] = $provider->getState();
    header('Location: ' . $authUrl);
    exit;

// Check given state against previously stored one to mitigate CSRF attack
} elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
    unset($_SESSION['oauth2state']);
    exit('Invalid state');
}

// Try to get an access token (using the authorization code grant)
$accessToken = $provider->getAccessToken('authorization_code', ['code' => $_GET['code']]);
// This token should be stored somewhere (e.g. in the session, since the expiration time is quite short)

// Now that you have a token you can look up a users profile data
try {
    /** @var \Alcohol\OAuth2\Client\Provider\EveOnlineResourceOwner $user */
    $user = $provider->getResourceOwner($accessToken);
    $html = <<<'HTML'
<pre>
CharacterID: %u
CharacterName: %s
ExpiresOn: %s
Scopes: %s
TokenType: %s
CharacterOwnerHash: %s
IntellectuelProperty: %s
</pre>
HTML;

    printf(
        $html,
        $user->getId(),
        $user->getName(),
        // expires token is in UTC timezone by default, i want to see when it expires relative to my local time
        $user->getExpiresOn()->setTimezone(new \DateTimeZone('Europe/Amsterdam'))->format(DATE_RFC3339),
        implode(', ', $user->getScopes()),
        $user->getTokenType(),
        $user->getCharacterOwnerHash(),
        $user->getIntellectualProperty()
    );
} catch (Exception $e) {
    // Failed to get user details
    exit('Oh dear...');
}
