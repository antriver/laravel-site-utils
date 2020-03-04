<?php

namespace Antriver\LaravelSiteScaffolding\Validation\Rules;

use Illuminate\Contracts\Validation\Rule;

class LongitudeValidationRule implements Rule
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
        return is_numeric($value) && $value >= -180 && $value <= 180;
    }

    /**
     * Get the validation error message.
     *
     * @return string|array
     */
    public function message()
    {
        return 'The :attribute must be between -180 and 180.';
    }
}
