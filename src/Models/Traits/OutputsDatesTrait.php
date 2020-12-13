<?php

namespace Antriver\LaravelSiteUtils\Models\Traits;

use Antriver\LaravelSiteUtils\Date\DateFormat;
use Carbon\Carbon;

/**
 * Provides a method to convert the dates of a model to ISO 8601
 */
trait OutputsDatesTrait
{
    protected function formatArrayDates(array $array)
    {
        foreach ($this->getDates() as $dateAttribute) {
            if (in_array($dateAttribute, $this->hidden)) {
                continue;
            }
            if (!array_key_exists($dateAttribute, $array)) {
                continue;
            }
            $value = $this->{$dateAttribute};
            if ($value) {
                /** @var Carbon $carbon */
                $carbon = $this->asDateTime($this->{$dateAttribute});
                $array[$dateAttribute] = $carbon->format(DateFormat::API_DATE_FORMAT);
            }
        }

        return $array;
    }
}
