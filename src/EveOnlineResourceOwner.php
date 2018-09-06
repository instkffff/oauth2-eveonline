<?php

/*
 * (c) Rob Bast <rob.bast@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Alcohol\OAuth2\Client\Provider;

use DateTime;
use DateTimeZone;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class EveOnlineResourceOwner implements ResourceOwnerInterface
{
    /**
     * @see http://eveonline-third-party-documentation.readthedocs.io/en/latest/sso/obtaincharacterid.html
     *
     * @var array
     */
    protected $response;

    public function __construct(array $response)
    {
        $this->response = $response;
    }

    public function getId(): int
    {
        return $this->response['CharacterID'];
    }

    public function getCharacterId(): int
    {
        return $this->getId();
    }

    public function getName(): string
    {
        return $this->response['CharacterName'];
    }

    public function getCharacterName(): string
    {
        return $this->getName();
    }

    public function getExpiresOn(): DateTime
    {
        return new DateTime($this->response['ExpiresOn'], new DateTimeZone('UTC'));
    }

    public function getScopes(): array
    {
        return explode(' ', $this->response['Scopes']);
    }

    public function getTokenType(): string
    {
        return $this->response['TokenType'];
    }

    public function getCharacterOwnerHash(): string
    {
        return $this->response['CharacterOwnerHash'];
    }

    public function getIntellectualProperty(): string
    {
        return $this->response['IntellectualProperty'];
    }

    public function toArray(): array
    {
        return $this->response;
    }
}
