<?php

namespace Antriver\LaravelSiteUtils\Validation\Validators;

use Config;
use Illuminate\Support\Arr;
use Illuminate\Validation\Validator;
use ReCaptcha\ReCaptcha;
use Request;

class RecaptchaValidator
{
    /**
     * Validate a recaptcha (or a lame captcha type thing given as recaptcha).
     *
     * @param $attribute
     * @param $value
     * @param $parameters
     * @param Validator $validator
     *
     * @return int
     */
    public function validate($attribute, $value, $parameters, $validator)
    {
        if (!is_string($value) || $value === '') {
            return false;
        }

        // First check if this is our "crapthca" instead of a real recaptcha.
        // It's a JSON string with k, h, and t like:
        // {"k":"username", "h":"$username+$t", "t": 12345}
        // Where:
        // k = the name of a key from the rest of the data
        // t = a timestamp
        // h = the value of item k from the data plus that timestamp
        // (simple and insecure!)
        try {
            $json = json_decode($value, false, 512, JSON_THROW_ON_ERROR);
            if (!empty($json) && !empty($json->k) && !empty($json->h) && !empty($json->t)) {
                // This is it.

                // Get the value of the k value (e.g. the username)
                $k = Arr::get($validator->getData(), $json->k);

                // $json->h should be k + $json->t
                $expected = hash('sha256', $k.$json->t.'undefined');

                return $json->h === $expected;
            }
        } catch (\Throwable $t) {
            // Invalid JSON. Continue to real recaptcha.
        }

        $clientIp = Request::getClientIp();
        $recaptcha = new ReCaptcha((string) Config::get('services.recaptcha.secret'));

        $response = $recaptcha->verify($value, $clientIp);

        return $response->isSuccess();
    }
}
