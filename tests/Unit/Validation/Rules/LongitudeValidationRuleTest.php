<?php

namespace Antriver\LaravelSiteUtilsTests\Unit\Validation\Rules;

use Antriver\LaravelSiteUtils\Validation\Rules\LongitudeValidationRule;
use Antriver\LaravelSiteUtilsTests\Unit\AbstractUnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class LongitudeValidationRuleTest extends AbstractUnitTestCase
{
    public static function dataForTestPasses(): array
    {
        return [
            [
                'input' => 'hello',
                'expect' => false
            ],
            [
                'input' => '',
                'expect' => false
            ],
            [
                'input' => '0',
                'expect' => true
            ],
            [
                'input' => 0,
                'expect' => true
            ],
            [
                'input' => 0.12345,
                'expect' => true
            ],
            [
                'input' => '0.12345',
                'expect' => true
            ],
            [
                'input' => -45,
                'expect' => true
            ],
            [
                'input' => -90,
                'expect' => true
            ],
            [
                'input' => 45,
                'expect' => true
            ],
            [
                'input' => 90,
                'expect' => true
            ],
            [
                'input' => -91,
                'expect' => true
            ],
            [
                'input' => 91,
                'expect' => true
            ],
            [
                'input' => -180.01,
                'expect' => false
            ],
            [
                'input' => 180.01,
                'expect' => false
            ],
        ];
    }

    #[DataProvider('dataForTestPasses')]
    public function testPasses($input, bool $expect)
    {
        $rule = new LongitudeValidationRule();
        $this->assertSame($expect, $rule->passes('lat', $input));
    }
}
