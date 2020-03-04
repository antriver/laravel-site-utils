<?php

namespace Antriver\LaravelSiteScaffolding\Validation\Rules;

use Illuminate\Contracts\Validation\Rule;

class LatitudeValidationRule implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return is_numeric($value) && $value >= -90 && $value <= 90;
    }

    /**
     * Get the validation error message.
     *
     * @return string|array
     */
    public function message()
    {
        return 'The :attribute must be between -90 and 90.';
    }
}
