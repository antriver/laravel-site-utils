<?php

namespace Antriver\LaravelSiteScaffolding\Debug;

use Symfony\Component\HttpFoundation\HeaderBag;

class HeaderBagFormatter
{
    public static function implodeValues(HeaderBag $headerBag)
    {
        return array_map(
            function ($headerValues) {
                return implode('; ', $headerValues);
            },
            $headerBag->all()
        );
    }
}
