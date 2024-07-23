<?php

namespace Antriver\LaravelSiteUtils\EmailVerification;

use Antriver\LaravelSiteUtils\Users\UserInterface;
use Antriver\LaravelRepositories\Base\AbstractRepository;

/**
 * @method findOrFail(int $id): EmailVerification
 */
class EmailVerificationRepository extends AbstractRepository
{
    /**
     * Return the fully qualified class name of the Models this repository returns.
     *
     * @return string
     */
    public function getModelClass(): string
    {
        return EmailVerification::class;
    }

    /**
     * @param UserInterface $user
     *
     * @return \Illuminate\Database\Eloquent\Collection|EmailVerification[]
     */
    public function findPendingVerifications(UserInterface $user)
    {
        return EmailVerification::where('userId', $user->getId())->orderBy('id')->get();
    }

    /**
     * @param UserInterface $user
     *
     * @return EmailVerification
     */
    /**
     * @param UserInterface $user
     *
     * @return \Illuminate\Database\Eloquent\Model|EmailVerification|null
     */
    public function findLatestPendingVerification(UserInterface $user): ?EmailVerification
    {
        return EmailVerification::where('userId', $user->getId())->orderBy('id', 'DESC')->first();
    }
}
