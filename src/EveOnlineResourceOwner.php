<?php

/*
 * (c) Rob Bast <rob.bast@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Alcohol\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class EveOnlineResourceOwner implements ResourceOwnerInterface
{
    /**
     * @see http://eveonline-third-party-documentation.readthedocs.io/en/latest/sso/obtaincharacterid.html
     *
     * @var array
     */
    protected $response;

    /**
     * @param $response
     */
    public function __construct($response)
    {
        $this->response = $response;
    }

    /**
     * @return int|null
     */
    public function getId(): int
    {
        return $this->response['CharacterID'];
    }

    /**
     * @return string|null
     */
    public function getName(): string
    {
        return $this->response['CharacterName'];
    }

    /**
     * @return \DateTime|null
     */
    public function getExpiresOn(): \DateTime
    {
        return new \DateTime($this->response['ExpiresOn'], new \DateTimeZone('UTC'));
    }

    /**
     * @return string|null
     */
    public function getScopes(): string
    {
        return $this->response['Scopes'];
    }

    /**
     * @return string|null
     */
    public function getTokenType(): string
    {
        return $this->response['TokenType'];
    }

    /**
     * @return string|null
     */
    public function getCharacterOwnerHash(): string
    {
        return $this->response['CharacterOwnerHash'];
    }

    /**
     * @return string|null
     */
    public function getIntellectualProperty(): string
    {
        return $this->response['IntellectualProperty'];
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->response;
    }
}
