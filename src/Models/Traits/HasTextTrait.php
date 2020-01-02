<?php

namespace Antriver\LaravelSiteUtils\Models\Traits;

use Antriver\LaravelSiteUtils\Models\Base\AbstractModel;

trait HasTextTrait
{
    public function getText()
    {
        /** @var AbstractModel $this */
        return $this->getAttribute('text');
    }

    public function getStrippedText()
    {
        // TODO
        return $this->getText();
    }
}
