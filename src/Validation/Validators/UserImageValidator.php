<?php

namespace Antriver\LaravelSiteUtils\Validation\Validators;

use Antriver\LaravelSiteUtils\Images\Image;
use Antriver\LaravelSiteUtils\Images\ImageRepository;

class UserImageValidator
{
    public function validate($attribute, $value, $parameters)
    {
        /** @var Image $image */
        $image = app(ImageRepository::class)->find($value);
        if (!$image) {
            return false;
        }

        if (!empty($parameters[0])) {
            $userId = $parameters[0];
            if ($image->getUserId() != $userId) {
                return false;
            }
        }

        return true;
    }
}
