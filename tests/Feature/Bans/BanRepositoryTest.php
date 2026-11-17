<?php

namespace Antriver\LaravelSiteUtilsTests\Feature\Bans;

use Antriver\LaravelSiteUtils\Bans\Ban;
use Antriver\LaravelSiteUtils\Bans\BanRepository;
use Antriver\LaravelSiteUtilsTests\Feature\Api\AbstractApiTestCase;
use Illuminate\Support\Facades\Cache;

class BanRepositoryTest extends AbstractApiTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Laravel 13's hardened default: cached values may not unserialize to objects.
        config(
            [
                'cache.default' => 'array',
                'cache.stores.array.serialize' => true,
                'cache.serializable_classes' => false,
            ]
        );
        app('cache')->forgetDriver('array');
    }

    public function testFindCurrentForUserReturnsBanOnCacheHit()
    {
        $user = $this->createUser();
        $ban = $this->createBan(['userId' => $user->id]);
        $repository = app(BanRepository::class);

        $repository->findCurrentForUser($user);
        $result = $repository->findCurrentForUser($user);

        $this->assertInstanceOf(Ban::class, $result);
        $this->assertSame($ban->id, $result->id);
    }

    public function testFindCurrentForIpReturnsBanOnCacheHit()
    {
        $ban = $this->createBan(['ip' => '192.0.2.10']);
        $repository = app(BanRepository::class);

        $repository->findCurrentForIp('192.0.2.10');
        $result = $repository->findCurrentForIp('192.0.2.10');

        $this->assertInstanceOf(Ban::class, $result);
        $this->assertSame($ban->id, $result->id);
    }

    public function testFindCurrentForUserIgnoresOldCacheKey()
    {
        $user = $this->createUser();
        $ban = $this->createBan(['userId' => $user->id]);
        Cache::forever('user-ban:'.$user->id, false);

        $result = app(BanRepository::class)->findCurrentForUser($user);

        $this->assertSame($ban->id, $result?->id);
    }

    public function testFindCurrentForUserReturnsNullWhenNotBanned()
    {
        $user = $this->createUser();
        $repository = app(BanRepository::class);

        $this->assertNull($repository->findCurrentForUser($user));
        $this->assertNull($repository->findCurrentForUser($user));
    }

    /**
     * Creates and saves a ban with the given attributes.
     */
    private function createBan(array $attributes): Ban
    {
        $ban = new Ban();
        $ban->forceFill(array_merge(['reason' => 'Testing'], $attributes));
        $ban->save();

        return $ban;
    }
}
