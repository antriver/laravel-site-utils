<?php

namespace Antriver\LaravelSiteScaffolding\EmailVerification\Http;

use Antriver\LaravelSiteScaffolding\Auth\Http\ApiAuthResponseFactory;
use Antriver\LaravelSiteScaffolding\Auth\UserAuthenticator;
use Antriver\LaravelSiteScaffolding\EmailVerification\EmailVerificationManager;
use Antriver\LaravelSiteScaffolding\EmailVerification\EmailVerificationRepository;
use Antriver\LaravelSiteScaffolding\Exceptions\ForbiddenHttpException;
use Antriver\LaravelSiteScaffolding\Http\Controllers\Base\AbstractApiController;
use Antriver\LaravelSiteScaffolding\Users\UserRepository;
use Antriver\LaravelSiteScaffolding\Users\ValidatesUserCredentialsTrait;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class EmailVerificationController extends AbstractApiController
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
     * @param EmailVerificationManager $emailVerificationManager
     * @param Request $request
     *
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws ForbiddenHttpException
     */
    public function show(
        $id,
        EmailVerificationManager $emailVerificationManager,
        Request $request
    ) {
        $currentUser = $this->getRequestUser($request);

        $emailVerification = $emailVerificationManager->findOrFail($id);

        if ($currentUser->id !== $emailVerification->userId) {
            throw new ForbiddenHttpException("Incorrect user.");
        }

        return $this->response(
            [
                'emailVerification' => $emailVerification,
            ]
        );
    }

    /**
     * @api {post} /email-verifications/:id/resend Resend a verification.
     *
     * @param EmailVerificationManager $emailVerificationManager
     * @param Request $request
     *
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws ForbiddenHttpException
     */
    public function resend(
        int $id,
        EmailVerificationManager $emailVerificationManager,
        Request $request
    ) {
        $currentUser = $this->getRequestUser($request);

        $emailVerification = $emailVerificationManager->findOrFail($id);

        if ($currentUser->id !== $emailVerification->userId) {
            throw new ForbiddenHttpException("Incorrect user.");
        }

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
     * @param EmailVerificationRepository $emailVerificationRepository
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(
        $id,
        EmailVerificationRepository $emailVerificationRepository
    ) {
        $emailVerification = $emailVerificationRepository->findOrFail($id);

        $this->authorize('destroy', $emailVerification);

        $success = $emailVerificationRepository->remove($emailVerification);

        return $this->successResponse($success);
    }

    /**
     * @param int $id
     * @param string $token
     * @param ApiAuthResponseFactory $apiAuthResponseFactory
     * @param EmailVerificationManager $emailVerificationManager
     * @param \Illuminate\Http\Request $request
     * @param UserAuthenticator $userAuthenticator
     * @param UserRepository $userRepository
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function verify(
        int $id,
        string $token,
        ApiAuthResponseFactory $apiAuthResponseFactory,
        EmailVerificationManager $emailVerificationManager,
        Request $request,
        UserAuthenticator $userAuthenticator,
        UserRepository $userRepository
    ) {
        $emailVerification = $emailVerificationManager->findOrFail($id);

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
}
