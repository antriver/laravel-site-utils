<?php

namespace Antriver\LaravelSiteUtils\Http\Controllers;

use Antriver\LaravelSiteUtils\Users\User;
use Antriver\LaravelSiteUtils\Users\UserInterface;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;

trait ControllerTrait
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
     * @return UserInterface|User|null
     */
    protected function getRequestUser(Request $request): ?UserInterface
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
