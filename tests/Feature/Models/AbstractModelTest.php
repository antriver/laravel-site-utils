<?php

namespace Antriver\LaravelSiteUtilsTests\Feature\Models;

use Antriver\LaravelSiteUtils\Date\DateFormat;
use Antriver\LaravelSiteUtils\EmailVerification\EmailVerification;
use Antriver\LaravelSiteUtilsTests\Feature\AbstractFeatureTest;
use Carbon\Carbon;
use Carbon\CarbonInterface;

class AbstractModelTest extends AbstractFeatureTest
{
    public function testGetDatesIncludesDatesProperty()
    {
        $this->assertEqualsCanonicalizing(['createdAt', 'resentAt'], (new EmailVerification())->getDates());
    }

    public function testDatesPropertyColumnsAreCarbon()
    {
        $this->assertInstanceOf(CarbonInterface::class, $this->makeVerification()->resentAt);
    }

    public function testToArrayFormatsDatesPropertyColumns()
    {
        $array = $this->makeVerification()->toArray();

        $this->assertSame(
            Carbon::parse('2026-01-02 03:04:05')->format(DateFormat::API_DATE_FORMAT),
            $array['createdAt']
        );
    }

    /**
     * Creates an unsaved email verification with both date columns set.
     */
    private function makeVerification(): EmailVerification
    {
        return (new EmailVerification())->setRawAttributes(
            [
                'createdAt' => '2026-01-02 03:04:05',
                'resentAt' => '2026-01-03 04:05:06',
            ]
        );
    }
}
