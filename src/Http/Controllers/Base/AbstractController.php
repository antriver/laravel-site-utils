<?php

namespace Antriver\LaravelSiteUtils\Http\Controllers\Base;

use Antriver\LaravelSiteUtils\Users\UserInterface;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as LaravelBaseController;

abstract class AbstractController extends LaravelBaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;

    public function __construct()
    {
        // Does nothing by default.
    }

    /**
     * @param Request $request
     *
     * @return UserInterface|null
     */
    protected function getRequestUser(Request $request)
    {
        return $request->user();
    }

    /**
     * Mark routes as requiring authentication.
     *
     * @param array $options
     *
     * @return \Illuminate\Routing\ControllerMiddlewareOptions
     */
    protected function requireAuth(array $options = [])
    {
        return $this->middleware('auth', $options);
    }
}
