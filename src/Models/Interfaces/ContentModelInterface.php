<?php

namespace Antriver\LaravelSiteUtils\Models\Interfaces;

interface ContentModelInterface
{
    public function getText(): string;

    public function getKey();

    public function getForeignKey();
}
