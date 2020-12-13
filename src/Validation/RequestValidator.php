<?php

namespace Antriver\LaravelSiteUtils\Validation;

use Illuminate\Contracts\Validation\Factory;
use Illuminate\Foundation\Validation\ValidatesRequests;

/**
 * An alternative to using the ValidatesRequests trait.
 * Inject RequestValidator and call $requestValidator->validate instead.
 */
class RequestValidator
{
    use ValidatesRequests;

    /**
     * @var Factory
     */
    private $factory;

    public function __construct(
        Factory $factory
    ) {
        $this->factory = $factory;
    }

    protected function getValidationFactory()
    {
        return $this->factory;
    }
}
