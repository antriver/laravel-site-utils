<?php

namespace Antriver\LaravelSiteUtils\Exceptions\Traits;

trait HasJwtTrait
{
    /**
     * JWT may be needed for the user to start re-verification or re-send an email.
     *
     * @var string|null
     */
    protected $jwt;

    /**
     * @return string|null
     */
    public function getJwt(): ?string
    {
        return $this->jwt;
    }

    /**
     * @param string|null $jwt
     */
    public function setJwt(?string $jwt)
    {
        $this->jwt = $jwt;
    }
}
