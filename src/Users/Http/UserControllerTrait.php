<?php

namespace Antriver\LaravelSiteUtils\Users\Http;

use Antriver\LaravelSiteUtils\EmailVerification\EmailVerificationManager;
use Antriver\LaravelSiteUtils\Users\User;
use Antriver\LaravelSiteUtils\Users\UserNameChange;
use Antriver\LaravelSiteUtils\Users\UserPresenter;
use Antriver\LaravelSiteUtils\Users\UserRepository;
use Illuminate\Http\Request;
use Tmd\LaravelPasswordUpdater\PasswordHasher;

trait UserControllerTrait
{
    public function __construct()
    {
        $this->requireAuth(['only' => 'update']);
    }

    /**
     * @api {patch} /users/:username Update A User
     * @apiGroup Users
     *
     * @param User $user
     * @param Request $request
     * @param UserPresenter $userPresenter
     * @param UserRepository $userRepository
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(
        User $user,
        EmailVerificationManager $emailVerificationManager,
        PasswordHasher $passwordHasher,
        Request $request,
        UserPresenter $userPresenter,
        UserRepository $userRepository
    ) {
        $this->authorize('update', $user);

        $this->validate(
            $request,
            [
                'avatarImageId' => 'nullable|user_image:'.$user->id,
            ]
        );

        // Change avatar
        if ($request->exists('avatarImageId')) {
            $user->avatarImageId = $request->input('avatarImageId') ?: null;
        }

        // Change username
        if ($request->exists('username')) {
            $oldUsername = $user->username;
            $newUsername = $request->input('username');
            if ($newUsername && $newUsername !== $oldUsername) {
                $user->username = $newUsername;
                UserNameChange::create(
                    [
                        'userId' => $user->id,
                        'oldName' => $oldUsername,
                        'newName' => $newUsername,
                    ]
                );
            }
        }

        // Change email
        if ($newEmail = $request->input('email')) {
            if ($newEmail != $user->email) {
                // We don't change the username on the model now, instead a verification request is emailed
                // and the email on the model is changed after clicking that verification link.
                $emailVerificationManager->sendEmailChangeVerification($user, $newEmail);
            }
        }

        // Change password
        if ($password = $request->input('password')) {
            $user->password = $passwordHasher->generateHash($password);
        }

        $userRepository->persist($user);

        $user = $userRepository->fresh($user);

        return $this->response(
            [
                'success' => true,
                'user' => $userPresenter->present($user),
            ]
        );
    }
}
