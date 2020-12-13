<?php

namespace Antriver\LaravelSiteUtils\Validation\Validators;

use Illuminate\Validation\Validator;

class EmptyValidator
{
    /**
     * This is invoked by the validator rule 'empty'
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
        $attributes = $validator->attributes();

        return empty($attributes[$attribute]);
    }
}
