<?php

namespace Antriver\LaravelSiteUtilsTests\Unit\Auth;

use Antriver\LaravelSiteUtils\Auth\JwtFactory;
use Antriver\LaravelSiteUtils\Users\UserInterface;
use Antriver\LaravelSiteUtilsTests\Unit\AbstractUnitTestCase;
use DomainException;
use Firebase\JWT\SignatureInvalidException;
use Illuminate\Config\Repository;

class JwtFactoryTest extends AbstractUnitTestCase
{
    private const KEY_A = 'base64:2fl+Ktvkfl+Fuz4Qp/A75G2RTiWVA/ZoKZvp6fiiM10=';

    private const KEY_B = 'base64:7mQ4u9WcXk2bVvJq8Hn3Rt5Yp1Ls6Df0Ga2Zx9Ce4Bo=';

    public function testDecodeTokenReturnsSubject()
    {
        $factory = $this->makeFactory(self::KEY_A);

        $payload = $factory->decodeToken($factory->generateToken($this->makeUser(42)));

        $this->assertSame(42, $payload->sub);
    }

    public function testDecodeTokenRejectsForeignSignature()
    {
        $token = $this->makeFactory(self::KEY_A)->generateToken($this->makeUser(42));

        $this->expectException(SignatureInvalidException::class);

        $this->makeFactory(self::KEY_B)->decodeToken($token);
    }

    public function testGenerateTokenRejectsShortKey()
    {
        $this->expectException(DomainException::class);

        $this->makeFactory('0123456789abcdef')->generateToken($this->makeUser(42));
    }

    /**
     * Creates a JwtFactory that signs with the given app key.
     */
    private function makeFactory(string $key): JwtFactory
    {
        return new JwtFactory(new Repository(['app' => ['key' => $key]]));
    }

    /**
     * Creates a user stub with the given ID.
     */
    private function makeUser(int $id): UserInterface
    {
        $user = $this->createStub(UserInterface::class);
        $user->method('getId')->willReturn($id);

        return $user;
    }
}
