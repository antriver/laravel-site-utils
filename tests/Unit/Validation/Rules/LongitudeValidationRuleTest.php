<?php

namespace Antriver\LaravelSiteUtilsTests\Unit\Validation\Rules;

use Antriver\LaravelSiteUtils\Validation\Rules\LongitudeValidationRule;
use Antriver\LaravelSiteUtilsTests\Unit\AbstractUnitTestCase;

class LongitudeValidationRuleTest extends AbstractUnitTestCase
{
    public function dataForTestPasses()
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

    /**
     * @dataProvider dataForTestPasses
     *
     * @param $input
     * @param bool $expect
     */
    public function testPasses($input, bool $expect)
    {
        $rule = new LongitudeValidationRule();
        $this->assertSame($expect, $rule->passes('lat', $input));
    }
}
