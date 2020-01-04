<?php

namespace Antriver\LaravelSiteScaffolding\Images;

trait MatchesImageMarkupTrait
{
    protected function matchImageMarkupInText($text)
    {
        $matches = [];
        preg_match_all('/\[IMAGE:([0-9]+)\]/i', $text, $matches);

        return $matches;
    }
}
