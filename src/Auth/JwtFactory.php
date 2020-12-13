<?php

namespace Antriver\LaravelSiteUtils\Auth;

use Antriver\LaravelSiteUtils\Users\UserInterface;
use Carbon\Carbon;
use Firebase\JWT\JWT;
use Illuminate\Config\Repository;

class JwtFactory
{
    /**
     * @var string
     */
    private $alg = 'HS256';

    /**
     * @var string
     */
    private $key;

    public function __construct(Repository $configRepository)
    {
        /**
         * You can add a leeway to account for when there is a clock skew times between
         * the signing and verifying servers. It is recommended that this leeway should
         * not be bigger than a few minutes.
         *
         * Source: http://self-issued.info/docs/draft-ietf-oauth-json-web-token.html#nbfDef
         */
        JWT::$leeway = 60; // $leeway in seconds

        $this->key = $configRepository->get('app.key');
    }

    /**
     * @param UserInterface $user
     * @param int $expiresInMinutes
     *
     * @return string
     */
    public function generateToken(UserInterface $user, $expiresInMinutes = 30): string
    {
        $data = [
            'iat' => (new Carbon())->format('U'),
            'exp' => (new Carbon('+'.$expiresInMinutes.' MINUTES'))->format('U'),
            'sub' => $user->getId(),
        ];

        $jwt = JWT::encode($data, $this->key, $this->alg);

        return $jwt;
    }

    public function decodeToken(string $jwt)
    {
        return JWT::decode($jwt, $this->key, [$this->alg]);
    }
}
