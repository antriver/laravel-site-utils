<?php

namespace Antriver\SiteUtils\Models\Traits;

use Antriver\SiteUtils\Models\Base\AbstractModel;

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
