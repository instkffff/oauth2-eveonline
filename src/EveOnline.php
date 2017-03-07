<?php

/*
 * (c) Rob Bast <rob.bast@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Alcohol\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;

class EveOnline extends AbstractProvider
{
    use BearerAuthorizationTrait;

    /**
     * The base URL for authorizing a client.
     *
     * @var string
     */
    protected $urlAuthorize = 'https://login.eveonline.com/oauth/authorize';

    /**
     * The base URL for requesting an access token.
     *
     * @var string
     */
    protected $urlAccessToken = 'https://login.eveonline.com/oauth/token';

    /**
     * The URL for requesting the resource owner's details.
     *
     * @var string
     */
    protected $urlResourceOwnerDetails = 'https://login.eveonline.com/oauth/verify';

    /**
     * Get authorization url to begin OAuth flow.
     *
     * @see http://eveonline-third-party-documentation.readthedocs.io/en/latest/sso/authentication.html#redirect-to-the-sso
     *
     * @return string
     */
    public function getBaseAuthorizationUrl(): string
    {
        return $this->urlAuthorize;
    }

    /**
     * Get access token url to retrieve token.
     *
     * @see http://eveonline-third-party-documentation.readthedocs.io/en/latest/sso/authentication.html#verify-the-authorization-code
     *
     * @param array $params
     *
     * @return string
     */
    public function getBaseAccessTokenUrl(array $params): string
    {
        return $this->urlAccessToken;
    }

    /**
     * Get provider url to fetch user details.
     *
     * @see http://eveonline-third-party-documentation.readthedocs.io/en/latest/sso/obtaincharacterid.html
     *
     * @param \League\OAuth2\Client\Token\AccessToken $token
     *
     * @return string
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token): string
    {
        return $this->urlResourceOwnerDetails;
    }

    /**
     * Requests and returns the resource owner of given access token.
     *
     * @param \League\OAuth2\Client\Token\AccessToken $token
     *
     * @return \Alcohol\OAuth2\Client\Provider\EveOnlineResourceOwner
     */
    public function getResourceOwner(AccessToken $token)
    {
        $response = $this->fetchResourceOwnerDetails($token);

        return $this->createResourceOwner($response, $token);
    }

    /**
     * Get the default scopes used by this provider.
     *
     * @return array
     */
    protected function getDefaultScopes(): array
    {
        return [];
    }

    /**
     * Returns the string that should be used to separate scopes when building
     * the URL for requesting an access token.
     *
     * @return string
     */
    protected function getScopeSeparator(): string
    {
        return ' ';
    }

    /**
     * Check a provider response for errors.
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param array|string $data
     *
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    protected function checkResponse(ResponseInterface $response, $data)
    {
        if ($response->getStatusCode() >= 400) {
            throw new IdentityProviderException(
                $data['error'] ?: $response->getReasonPhrase(),
                $response->getStatusCode(),
                $response
            );
        }
    }

    /**
     * Generate a user object from a successful user details request.
     *
     * @param array $response
     * @param \League\OAuth2\Client\Token\AccessToken $token
     *
     * @return \Alcohol\OAuth2\Client\Provider\EveOnlineResourceOwner
     */
    protected function createResourceOwner(array $response, AccessToken $token): EveOnlineResourceOwner
    {
        return new EveOnlineResourceOwner($response);
    }
}
