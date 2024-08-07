<?php

namespace Antriver\LaravelSiteUtils\UserSocialAccounts;

use Antriver\LaravelSiteUtils\Users\UserInterface;
use Antriver\LaravelRepositories\Base\AbstractRepository;

class UserSocialAccountRepository extends AbstractRepository
{
    /**
     * Return the fully qualified class name of the Models this repository returns.
     *
     * @return string
     */
    public function getModelClass(): string
    {
        return UserSocialAccount::class;
    }

    public static function getSupportedServices()
    {
        return [
            'facebook',
            'google',
            'tumblr',
            'twitter',
        ];
    }

    /**
     * @param UserInterface $user
     * @param bool $indexByService
     *
     * @return UserSocialAccount[]|UserSocialAccount[][]|\Illuminate\Database\Eloquent\Collection
     */
    public function findAccountsForUser(UserInterface $user, $indexByService = false)
    {
        $accounts = UserSocialAccount::where('userId', $user->getId())->get();

        if ($indexByService) {
            return $this->indexAccountsByService($accounts);
        }

        return $accounts;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Collection|UserSocialAccount[] $accounts
     *
     * @return UserSocialAccount[][]
     */
    public function indexAccountsByService($accounts)
    {
        $services = [];
        foreach (self::getSupportedServices() as $service) {
            $services[$service] = [];
        }

        foreach ($accounts as $account) {
            $services[$account->service][] = $account;
        }

        return $services;
    }

    /**
     * @param string $service
     * @param string $serviceUserId
     *
     * @return UserSocialAccount|null
     */
    public function findByServiceUserId($service, $serviceUserId)
    {
        return UserSocialAccount::where('service', $service)->where('serviceUserId', $serviceUserId)->first();
    }

    /**
     * @param string $service
     * @param string $serviceUserId
     *
     * @return UserSocialAccount
     */
    public function findByServiceUserIdOrCreate($service, $serviceUserId)
    {
        if ($account = $this->findByServiceUserId($service, $serviceUserId)) {
            return $account;
        }

        return new UserSocialAccount(
            [
                'service' => $service,
                'serviceUserId' => $serviceUserId,
            ]
        );
    }
}
