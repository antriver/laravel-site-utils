<?php

namespace Antriver\LaravelSiteUtils\Mail;

interface MailStylesInterface
{
    public function getStyles(): array;

    public function getFontFamily(): string;

    public function getContactUrl(): string;

    public function getLogoUrl(): string;
}
