<?php

namespace Antriver\LaravelSiteUtils\Mail;

interface MailStylesInterface
{
    public function getStyles(): array;

    public function getFontFamily(): string;
}
