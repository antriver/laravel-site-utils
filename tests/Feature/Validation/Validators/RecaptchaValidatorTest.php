<?php

namespace Antriver\LaravelSiteUtilsTests\Feature\Validation\Validators;

use Antriver\LaravelSiteUtils\Validation\Validators\RecaptchaValidator;
use Antriver\LaravelSiteUtilsTests\Feature\AbstractFeatureTest;
use Illuminate\Validation\Validator;

class RecaptchaValidatorTest extends AbstractFeatureTest
{
    public function testRejectsArrayValue()
    {
        $this->assertFalse((new RecaptchaValidator())->validate('captcha', ['x'], [], null));
    }

    public function testRejectsEmptyValue()
    {
        $this->assertFalse((new RecaptchaValidator())->validate('captcha', '', [], null));
    }

    public function testAcceptsCrapthca()
    {
        $value = json_encode(
            [
                'k' => 'username',
                'h' => hash('sha256', 'bob'.'12345'.'undefined'),
                't' => 12345,
            ]
        );

        $this->assertTrue((new RecaptchaValidator())->validate('captcha', $value, [], $this->makeValidator()));
    }

    public function testRejectsCrapthcaWithWrongHash()
    {
        $value = json_encode(['k' => 'username', 'h' => 'nope', 't' => 12345]);

        $this->assertFalse((new RecaptchaValidator())->validate('captcha', $value, [], $this->makeValidator()));
    }

    /**
     * Creates a validator stub whose data contains a username of "bob".
     */
    private function makeValidator(): Validator
    {
        $validator = $this->createStub(Validator::class);
        $validator->method('getData')->willReturn(['username' => 'bob']);

        return $validator;
    }
}
