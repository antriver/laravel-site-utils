<?php

namespace Antriver\LaravelSiteScaffolding\Testing\Traits;

use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * Meta trait that contains all the other traits, plus some from Laravel.
 * DatabaseTransactions trait should be used too but is not used here to make it easier to comment out.
 */
trait TestCaseTrait
{
    use AssertionsTrait;
    //use DatabaseTransactions;
}
