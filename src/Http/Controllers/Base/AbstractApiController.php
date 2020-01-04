<?php

namespace Antriver\LaravelSiteScaffolding\Http\Controllers\Base;

use Antriver\LaravelModelPresenters\ModelPresenterInterface;
use Antriver\LaravelSiteScaffolding\Users\UserInterface;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @apiDefine GenericSuccessResponse
 * @apiSuccessExample {json} Success Response
 *     {
 *       "success": true
 *     }
 */

/**
 * @apiDefine RequiresAuthentication
 * @apiParam {string} sessionToken This endpoint requires authentication so a session token must be provided.
 */
abstract class AbstractApiController extends AbstractController
{
    /**
     * Create a Response. Attaches the default data.
     *
     * @param array $data
     * @param int $status
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function response($data = [], $status = 200)
    {
        $data['responseInfo'] = [
            'generatedAt' => (new Carbon())->toAtomString(),
            'generatedIn' => microtime(true) - LARAVEL_START,
        ];

        return Response::json($data, $status);
    }

    /**
     * @param bool|true $success
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function successResponse($success = true)
    {
        return $this->response(['success' => $success]);
    }

    /**
     * @param UserInterface $user
     */
    protected function requireVerifiedAccount(UserInterface $user)
    {
        if (!$user->isEmailVerified()) {
            throw new BadRequestHttpException('You need to verify your email address before you can do that.');
        }
    }

    /**
     * @param LengthAwarePaginator $paginator
     * @param string $dataKey
     * @param ModelPresenterInterface|null $presenter
     *
     * @return array
     */
    protected function paginatorToArray(
        LengthAwarePaginator $paginator,
        string $dataKey = 'items',
        ModelPresenterInterface $presenter = null
    ) {
        $request = app(Request::class);
        $paginator->appendsArray($request->query->all());

        $items = $presenter ? $presenter->presentArray($paginator->items()) : $paginator->items();

        return [
            $dataKey => $items,
            'pagination' => [
                'elements' => array_values($paginator->getElements()),
                'total' => (int) $paginator->total(),
                'perPage' => (int) $paginator->perPage(),
                'currentPage' => (int) $paginator->currentPage(),
                'lastPage' => (int) $paginator->lastPage(),
                'nextPageUrl' => $paginator->nextPageUrl(),
                'prevPageUrl' => $paginator->previousPageUrl(),
                'from' => (int) $paginator->firstItem(),
                'to' => (int) $paginator->lastItem(),
            ],
        ];
    }
}
