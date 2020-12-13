<?php

namespace Antriver\LaravelSiteUtils\EmailVerification\Http;

use Antriver\LaravelSiteUtils\Auth\ApiAuthResponseFactory;
use Antriver\LaravelSiteUtils\Auth\UserAuthenticator;
use Antriver\LaravelSiteUtils\EmailVerification\EmailVerification;
use Antriver\LaravelSiteUtils\EmailVerification\EmailVerificationManager;
use Antriver\LaravelSiteUtils\EmailVerification\EmailVerificationRepository;
use Antriver\LaravelSiteUtils\Users\User;
use Antriver\LaravelSiteUtils\Users\UserRepository;
use Antriver\LaravelSiteUtils\Users\ValidatesUserCredentialsTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

trait EmailVerificationControllerTrait
{
    use ValidatesUserCredentialsTrait;

    public function __construct()
    {
        $this->requireAuth();

        parent::__construct();
    }

    /**
     * @api {get} /email-verifications Get latest pending email verification for user.
     *
     * @param EmailVerificationRepository $emailVerificationRepository
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(
        EmailVerificationRepository $emailVerificationRepository,
        Request $request
    ) {
        /** @var User $currentUser */
        $currentUser = $this->getRequestUser($request);

        $emailVerification = $emailVerificationRepository->findLatestPendingVerification($currentUser);

        return $this->response(
            [
                'isVerified' => $currentUser->isEmailVerified(),
                'emailVerification' => $emailVerification,
            ]
        );
    }

    /**
     * @api {post} /email-verifications Verify a new email address
     *
     * @param EmailVerificationManager $emailVerificationManager
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(
        Request $request,
        EmailVerificationManager $emailVerificationManager
    ) {
        $user = $this->getRequestUser($request);

        $this->validate(
            $request,
            [
                'email' => $this->getEmailValidationRules($user),
            ]
        );

        $email = $request->input('email');
        if ($user->email && $email !== $user->email) {
            $emailVerification = $emailVerificationManager->sendEmailChangeVerification(
                $user,
                $email
            );
        } else {
            $emailVerification = $emailVerificationManager->sendReverificationEmail(
                $user,
                $email
            );
        }

        return $this->response(
            [
                'emailVerification' => $emailVerification,
            ]
        );
    }

    /**
     * @api {get} /email-verifications/:id Get pending email verification.
     *
     * @param $id
     * @param EmailVerificationRepository $emailVerificationRepository
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(
        $id,
        EmailVerificationRepository $emailVerificationRepository,
        Request $request
    ) {
        $emailVerification = $this->loadVerificationForCurrentUser($id, $request, $emailVerificationRepository);

        return $this->response(
            [
                'emailVerification' => $emailVerification,
            ]
        );
    }

    /**
     * @api {post} /email-verifications/:id/resend Resend a verification.
     *
     * @param int $id
     * @param EmailVerificationManager $emailVerificationManager
     * @param EmailVerificationRepository $emailVerificationRepository
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function resend(
        int $id,
        EmailVerificationManager $emailVerificationManager,
        EmailVerificationRepository $emailVerificationRepository,
        Request $request
    ) {
        /** @var User $currentUser */
        $currentUser = $this->getRequestUser($request);

        $emailVerification = $this->loadVerificationForCurrentUser($id, $request, $emailVerificationRepository);

        $emailVerificationManager->resendEmail($emailVerification, $currentUser);

        return $this->response(
            [
                'success' => true,
                'emailVerification' => $emailVerification,
            ]
        );
    }

    /**
     * @api {delete} /email-verifications/:id Cancel an email verification.
     *
     * @param $id
     * @param Request $request
     * @param EmailVerificationRepository $emailVerificationRepository
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(
        $id,
        Request $request,
        EmailVerificationRepository $emailVerificationRepository
    ) {
        $emailVerification = $this->loadVerificationForCurrentUser($id, $request, $emailVerificationRepository);

        $this->authorize('destroy', $emailVerification);

        $success = $emailVerificationRepository->remove($emailVerification);

        return $this->successResponse($success);
    }

    /**
     * @api {post} /email-verifications/:id/verify
     *
     * @param int $id
     * @param ApiAuthResponseFactory $apiAuthResponseFactory
     * @param EmailVerificationManager $emailVerificationManager
     * @param EmailVerificationRepository $emailVerificationRepository
     * @param \Illuminate\Http\Request $request
     * @param UserAuthenticator $userAuthenticator
     * @param UserRepository $userRepository
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function verify(
        int $id,
        ApiAuthResponseFactory $apiAuthResponseFactory,
        EmailVerificationManager $emailVerificationManager,
        EmailVerificationRepository $emailVerificationRepository,
        Request $request,
        UserAuthenticator $userAuthenticator,
        UserRepository $userRepository
    ) {
        $this->validate(
            $request,
            [
                'verificationToken' => 'required',
            ]
        );
        $token = $request->input('verificationToken');

        $emailVerification = $this->loadVerificationForCurrentUser($id, $request, $emailVerificationRepository);

        if ($token !== $emailVerification->token) {
            throw new BadRequestHttpException('Invalid token.');
        }

        $emailVerificationManager->verify($emailVerification);

        $user = $userRepository->findOrFail($emailVerification->userId);

        // Throw an exception if user cannot login.
        $userAuthenticator->ensureAccountCanLogin($user);

        $response = $apiAuthResponseFactory->make($request, $user);
        $response['success'] = true;

        return $this->response($response);
    }

    private function loadVerificationForCurrentUser(
        $id,
        Request $request,
        EmailVerificationRepository $emailVerificationRepository
    ): EmailVerification {
        /** @var User $currentUser */
        $currentUser = $this->getRequestUser($request);

        /** @var EmailVerification|null $emailVerification */
        $emailVerification = $emailVerificationRepository->find($id);

        if (!$emailVerification || $currentUser->id !== $emailVerification->userId) {
            throw (new ModelNotFoundException())->setModel(EmailVerification::class, $id);
        }

        return $emailVerification;
    }
}
