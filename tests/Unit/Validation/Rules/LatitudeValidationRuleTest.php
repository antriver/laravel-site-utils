<?php

namespace Antriver\LaravelSiteUtilsTests\Unit\Validation\Rules;

use Antriver\LaravelSiteUtils\Validation\Rules\LatitudeValidationRule;
use Antriver\LaravelSiteUtilsTests\Unit\AbstractUnitTestCase;

class LatitudeValidationRuleTest extends AbstractUnitTestCase
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
                'expect' => false
            ],
            [
                'input' => 91,
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
        $rule = new LatitudeValidationRule();
        $this->assertSame($expect, $rule->passes('lat', $input));
    }
}
