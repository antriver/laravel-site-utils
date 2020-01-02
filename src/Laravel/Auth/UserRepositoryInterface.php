<?php

namespace Antriver\SiteUtils\Laravel\Auth;

use Tmd\LaravelRepositories\Interfaces\RepositoryInterface;

/**
 * Marker interface to help with injecting the correct repository that returns
 * User models.
 */
interface UserRepositoryInterface extends RepositoryInterface
{

}
