<?php

namespace Antriver\LaravelSiteUtils\Models\Interfaces;

interface LinkableInterface
{
    /**
     * Return the relative path to this item (on the frontend).
     *
     * @return string
     */
    public function getUrl(): string;
}
