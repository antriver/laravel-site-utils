<?php
/**
 * Validator to test that the value isn't present
 */
declare(strict_types=1);

namespace Antriver\LaravelSiteUtils\Validation\Validators;

use Illuminate\Support\Arr;
use Illuminate\Validation\Validator;

class NotPresentValidator
{
    /**
     * This is invoked by the validator rule 'not_present'
     *
     * @param $attribute string the attribute name that is validating
     * @param $value mixed the value that we're testing
     * @param $parameters array
     * @param $validator Validator The Validator instance
     *
     * @return bool
     */
    public function validate($attribute, $value, $parameters = [], Validator $validator = null): bool
    {
        return !Arr::has($validator->attributes(), $attribute);
    }
}
